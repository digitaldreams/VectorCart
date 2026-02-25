<?php

namespace App\Repository;

use App\Entity\SearchQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SearchQuery>
 */
class SearchQueryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchQuery::class);
    }

    public function save(SearchQuery $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findByKeywordAndUser(string $keyword, ?int $userId): ?SearchQuery
    {
        $qb = $this->createQueryBuilder('sq')
            ->where('LOWER(sq.keyword) = :keyword')
            ->setParameter('keyword', strtolower($keyword))
            ->orderBy('sq.createdAt', 'DESC')
            ->setMaxResults(1);

        if ($userId !== null) {
            $qb->andWhere('sq.userId = :userId')
                ->setParameter('userId', $userId);
        } else {
            $qb->andWhere('sq.userId IS NULL');
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findUserSearchHistory(int $userId, int $limit = 20): array
    {
        return $this->createQueryBuilder('sq')
            ->where('sq.userId = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('sq.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findPopularSearches(int $limit = 10): array
    {
        return $this->createQueryBuilder('sq')
            ->where('sq.userId IS NULL')
            ->orderBy('sq.searchCount', 'DESC')
            ->addOrderBy('sq.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
