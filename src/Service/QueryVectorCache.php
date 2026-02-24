<?php

namespace App\Service;

use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Pgvector\Vector;

class QueryVectorCache
{
    public function __construct(
        private CacheInterface $cache,
        private VectorizerInterface $ollama
    ) {
    }

    /**
     * Get or compute and cache the embedding vector for a search query.
     *
     * @param string $query The search query text
     * @param int $dimensions The number of dimensions for the embedding
     * @return Vector The cached or newly computed vector
     */
    public function getVector(string $query, int $dimensions = 1536): Vector
    {
        // Create a cache key from the query and dimensions
        $cacheKey = 'query_vector_' . hash('xxh128', $query . '_' . $dimensions);

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($query, $dimensions) {
            // Set cache TTL to 1 hour (adjust as needed)
            $item->expiresAfter(3600);

            // Compute the vector
            $result = $this->ollama->vectorize($query, ['dimensions' => $dimensions]);

            return new Vector($result->getData());
        });
    }

    /**
     * Clear the cached vector for a specific query.
     *
     * @param string $query The search query text
     */
    public function clear(string $query): void
    {
        $cacheKey = 'query_vector_' . hash('xxh128', $query);
        $this->cache->delete($cacheKey);
    }

    /**
     * Clear all cached query vectors.
     */
    public function clearAll(): void
    {
        // Clear all items with query_vector_ prefix
        $this->cache->clear();
    }
}
