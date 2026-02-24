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
