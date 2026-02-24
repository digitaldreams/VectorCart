<?php
// src/Repository/ProductRepository.php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Pgvector\Vector;


class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function save(Product $product, bool $flush = false): void
    {
        $this->getEntityManager()->persist($product);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function searchByVector(array $queryEmbedding, int $limit = 10, ?string $category = null): array
    {
        $embeddingJson = '[' . implode(',', $queryEmbedding) . ']';
        $sql = "SELECT p.*, 1 - (embedding <=> :embedding) as score
                FROM products p
                WHERE p.in_stock = true AND 1 - (embedding <=> :embedding) > 0.75";

        $params = ['embedding' => $embeddingJson, 'limit' => $limit];
        $types = ['embedding' => 'string', 'limit' => 'integer'];

        if ($category) {
            $sql .= " AND p.category = :category";
            $params['category'] = $category;
            $types['category'] = 'string';
        }

        $sql .= " ORDER BY p.embedding <=> :embedding
                  LIMIT :limit";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql, $params, $types);

        // Convert results to Product entities with similarity scores
        $results = $stmt->fetchAllAssociative();
        $products = [];

        foreach ($results as $row) {
            $product = $this->find($row['id']);
            if ($product) {
                // Set the similarity score as a transient property
                $product->setSimilarityScore(round((float) $row['score'], 4));
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Search products using DQL with pgvector cosine_distance function.
     * Uses Doctrine DQL instead of raw SQL for better portability and type safety.
     *
     * @param array<float> $queryEmbedding The query embedding vector
     * @param int $limit Maximum number of results to return
     * @param string|null $category Optional category filter
     * @param float $minScore Minimum similarity score threshold (0.0 to 1.0)
     * @param int $offset Number of results to skip (for pagination)
     * @return array<Product>
     */
    public function searchByDql(array $queryEmbedding, int $limit = 10, ?string $category = null, float $minScore = 0.75, int $offset = 0): array
    {
        $vector = new Vector($queryEmbedding);

        $qb = $this->createQueryBuilder('p')
            ->select('p', '1 - cosine_distance(p.embedding, :embedding) AS score')
            ->where('p.inStock = true')
            ->andWhere('1 - cosine_distance(p.embedding, :embedding) >= :minScore')
            ->orderBy('score', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('embedding', $vector)
            ->setParameter('minScore', $minScore);

        if ($category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        $results = $qb->getQuery()->getResult();

        // Set similarity score on each product entity
        $products = [];
        foreach ($results as $row) {
            $product = $row[0];
            $score = (float) $row['score'];
            $product->setSimilarityScore(round($score, 4));
            $products[] = $product;
        }

        return $products;
    }

    /**
     * Count total search results for a given query vector.
     * Used for pagination.
     *
     * @param array<float> $queryEmbedding The query embedding vector
     * @param string|null $category Optional category filter
     * @param float $minScore Minimum similarity score threshold (0.0 to 1.0)
     * @return int Total number of matching results
     */
    public function countSearchResults(array $queryEmbedding, ?string $category = null, float $minScore = 0.75): int
    {
        $vector = new Vector($queryEmbedding);

        $qb = $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->where('p.inStock = true')
            ->andWhere('1 - cosine_distance(p.embedding, :embedding) >= :minScore')
            ->setParameter('embedding', $vector)
            ->setParameter('minScore', $minScore);

        if ($category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function findByCategory(string $category): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.category = :category')
            ->andWhere('p.inStock = true')
            ->setParameter('category', $category)
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function test()
    {
        $results = $this->getEntityManager()->createQuery(
            'SELECT i FROM App\Entity\Product i ORDER BY cosine_distance(i.embedding, :embedding)'
        )
            ->setParameter('embedding', new Vector([1, 2, 3]))
            ->setMaxResults(5)
            ->getResult();
    }

    public function getCategories(){
      return  $this->createQueryBuilder('p')
            ->select('DISTINCT p.category')
            ->getQuery()
            ->getResult();
    }
}
