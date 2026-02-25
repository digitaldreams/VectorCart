<?php

namespace App\Service;

use App\Entity\SearchQuery;
use App\Repository\SearchQueryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\AI\Store\Document\VectorizerInterface;
use Pgvector\Vector;

class QueryVectorCache
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SearchQueryRepository $searchQueryRepository,
        private VectorizerInterface $ollama
    ) {
    }

    /**
     * Get or compute and cache the embedding vector for a search query.
     * Uses database storage instead of cache component for persistence.
     *
     * @param string $query The search query text
     * @param int|null $userId The logged-in user ID (null for anonymous)
     * @param int $dimensions The number of dimensions for the embedding
     * @return Vector The cached or newly computed vector
     */
    public function getVector(string $query, ?int $userId = null, int $dimensions = 1536): Vector
    {
        // Try to find existing search query
        $searchQuery = $this->searchQueryRepository->findByKeywordAndUser($query, $userId);

        if ($searchQuery && $searchQuery->getVector()) {
            // Increment search count for existing query
            $searchQuery->incrementSearchCount();
            $this->entityManager->flush();

            return $searchQuery->getVector();
        }

        // Compute new vector
        $vectorResult = $this->ollama->vectorize($query, ['dimensions' => $dimensions]);
        $vectorArray = $vectorResult->getData();

        // Create new search query record
        if (!$searchQuery) {
            $searchQuery = new SearchQuery();
            $searchQuery->setKeyword($query)
                ->setUserId($userId);
            // Pass array directly - entity will convert to Vector
            $searchQuery->setVector($vectorArray);
            $this->entityManager->persist($searchQuery);
        } else {
            // Update existing record with vector
            $searchQuery->setVector($vectorArray)
                ->incrementSearchCount();
        }

        $this->entityManager->flush();

        return new Vector($vectorArray);
    }

    /**
     * Get search history for a user.
     *
     * @param int $userId The user ID
     * @param int $limit Maximum number of results
     * @return SearchQuery[]
     */
    public function getUserSearchHistory(int $userId, int $limit = 20): array
    {
        return $this->searchQueryRepository->findUserSearchHistory($userId, $limit);
    }

    /**
     * Get popular searches across all anonymous users.
     *
     * @param int $limit Maximum number of results
     * @return SearchQuery[]
     */
    public function getPopularSearches(int $limit = 10): array
    {
        return $this->searchQueryRepository->findPopularSearches($limit);
    }

    /**
     * Clear old search queries (optional cleanup).
     *
     * @param int $daysOld Delete queries older than this many days
     */
    public function cleanupOldSearches(int $daysOld = 90): void
    {
        $cutoffDate = new \DateTimeImmutable("-{$daysOld} days");
        
        $this->entityManager->createQueryBuilder()
            ->delete(SearchQuery::class, 'sq')
            ->where('sq.createdAt < :cutoff')
            ->setParameter('cutoff', $cutoffDate)
            ->getQuery()
            ->execute();
        
        $this->entityManager->flush();
    }
}
