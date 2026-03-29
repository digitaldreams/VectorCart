# Symfony AI with PostgreSQL pgvector: A Complete Guide

This guide covers everything you need to know about integrating Symfony AI with PostgreSQL pgvector for building RAG (Retrieval-Augmented Generation) applications.

## Table of Contents

1. [What is Symfony AI Store?](#what-is-symfony-ai-store)
2. [What is pgvector?](#what-is-pgvector)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Setting Up the Vector Store](#setting-up-the-vector-store)
6. [Core Concepts](#core-concepts)
7. [Usage Examples](#usage-examples)
8. [Common Errors and Solutions](#common-errors-and-solutions)

---

## What is Symfony AI Store?

The **Symfony AI Store** component is a low-level abstraction for storing and retrieving documents in vector stores. It's designed for **RAG (Retrieval-Augmented Generation)** use cases, enabling applications to dynamically extend context with semantically similar information.

### Key Components

| Component | Description |
|-----------|-------------|
| **Indexer** | Converts documents to embeddings and stores them |
| **Retriever** | Searches for documents based on semantic similarity |
| **StoreInterface** | Core interface with `add()` and `query()` methods |
| **VectorizerInterface** | Converts text to vector embeddings |

### Supported Stores

Symfony AI supports 20+ vector stores including:
- **Cloud services**: Pinecone, Weaviate, Supabase, MongoDB Atlas
- **Databases**: PostgreSQL, MariaDB, SurrealDB, Neo4j
- **Search engines**: Meilisearch, Qdrant, Milvus, Chroma, Typesense

---

## What is pgvector?

**pgvector** is an open-source PostgreSQL extension that adds vector similarity search capabilities. It allows you to:

- Store vector embeddings alongside your regular data
- Perform similarity searches using cosine distance, L2 distance, or inner product
- Leverage PostgreSQL's ACID compliance and indexing capabilities

### Distance Metrics

| Metric | Use Case |
|--------|----------|
| `cosine` | Most common for text embeddings |
| `l2` (Euclidean) | General purpose distance |
| `inner_product` | When vectors are normalized |

---

## Installation

### Step 1: Install Required Packages

```bash
composer require symfony/ai-bundle symfony/ai-postgres-store
```

### Step 2: Ensure PostgreSQL with pgvector

Make sure your PostgreSQL instance has the `pgvector` extension enabled. If using Docker:

```yaml
# compose.yaml
services:
  database:
    image: pgvector/pgvector:pg16
    environment:
      POSTGRES_DB: '%env(resolve:DATABASE_URL)%'
      POSTGRES_USER: '%env(resolve:POSTGRES_USER)%'
      POSTGRES_PASSWORD: '%env(resolve:POSTGRES_PASSWORD)%'
```

### Step 3: Configure Doctrine for pgvector Types

```yaml
# config/packages/doctrine.yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        types:
            vector: Pgvector\Doctrine\VectorType
            halfvec: Pgvector\Doctrine\HalfVectorType
            sparsevec: Pgvector\Doctrine\SparseVectorType
        dql:
            string_functions:
                cosine_distance: Pgvector\Doctrine\CosineDistance
                l2_distance: Pgvector\Doctrine\L2Distance
                max_inner_product: Pgvector\Doctrine\MaxInnerProduct
```

---

## Configuration

### Basic AI Configuration

```yaml
# config/packages/ai.yaml
ai:
    platform:
        ollama:
            host: '%env(OLLAMA_HOST)%'
        openai:
            api_key: '%env(OPENAI_API_KEY)%'

    agent:
        default:
            platform: 'ai.platform.ollama'
            model: 'llama3.2'

    vectorizer:
        default:
            platform: 'ai.platform.ollama'
            model: 'mxbai-embed-large'
            options:
                dimensions: 1024
```

### PostgreSQL Store Configuration

```yaml
# config/packages/ai_postgres_store.yaml
ai:
    store:
        postgres:
            default:
                dbal_connection: 'doctrine.dbal.default_connection'
                table_name: 'product_store'
                vector_field: 'embedding'
                distance: 'cosine'
```

### Configuration Options Explained

| Option | Description |
|--------|-------------|
| `dbal_connection` | Reference to Doctrine's DBAL connection service |
| `table_name` | Name of the table to store vectors |
| `vector_field` | Column name for the vector data |
| `distance` | Distance metric (`cosine`, `l2`, `inner_product`) |

> **Why use `dbal_connection`?** It reuses Doctrine's existing connection, ensuring consistent configuration and avoiding duplicate DSN settings.

---

## Setting Up the Vector Store

### Create the Vector Table

```bash
php bin/console ai:store:setup ai.store.postgres.default
```

This command creates the necessary table with pgvector columns. For production, consider using Doctrine Migrations instead.

### Verify Setup

```bash
php bin/console list ai
```

---

## Core Concepts

### Document Types

```php
use Symfony\AI\Store\Document\TextDocument;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Document\Metadata;

// Text document (auto-vectorized)
$textDoc = new TextDocument('Your content here');

// Vector document (pre-computed vector)
$vectorDoc = new VectorDocument(
    id: Uuid::v4(),
    vector: $vector,
    metadata: new Metadata(['type' => 'blog_post', 'author' => 'John'])
);
```

### Vectorization

```php
use Symfony\AI\Store\Document\VectorizerInterface;

// Vectorize text
$vector = $vectorizer->vectorize('I love my family', ['dimensions' => 1024]);
```

### Storing Documents

```php
use Symfony\AI\Store\StoreInterface;

$store->add($document);
// Or multiple documents
$store->add($doc1, $doc2, $doc3);
```

### Querying Documents

```php
use Symfony\AI\Store\Query\VectorQuery;

// Create a query vector
$queryVector = $vectorizer->vectorize('search query');

// Query the store (NOTE: Must wrap in VectorQuery)
$results = $store->query(new VectorQuery($queryVector), [
    'limit' => 5,
    'where' => "metadata->>'type'=:type",
    'params' => ['type' => 'blog_post']
]);

foreach ($results as $result) {
    echo $result->getScore();  // Similarity score
    echo $result->metadata['content'];  // Access metadata
}
```

---

## Usage Examples

### Complete Controller Example

```php
<?php

namespace App\Controller;

use Symfony\AI\Platform\Vector\Vector;
use Symfony\AI\Store\Document\Metadata;
use Symfony\AI\Store\Document\VectorDocument;
use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\AI\Store\Query\VectorQuery;
use Symfony\AI\Store\StoreInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/api')]
final class VectorController extends AbstractController
{
    #[Route('/store/save', methods: ['POST'])]
    public function store(
        #[Autowire(service: 'ai.store.postgres.default')] StoreInterface $store,
        #[Autowire(service: 'ai.vectorizer.default')] VectorizerInterface $vectorizer,
        Request $request
    ): JsonResponse {
        $text = $request->request->get('text', 'Default text');
        $expert = $request->request->get('expert', 'General');

        // Vectorize the text
        $vector = $vectorizer->vectorize($text, ['dimensions' => 1024]);

        // Create and store document
        $document = new VectorDocument(
            id: Uuid::v4(),
            vector: $vector,
            metadata: new Metadata(['text' => $text, 'expert' => $expert])
        );

        $store->add($document);

        return $this->json(['status' => 'Document stored successfully']);
    }

    #[Route('/store/query', methods: ['GET'])]
    public function query(
        #[Autowire(service: 'ai.store.postgres.default')] StoreInterface $store,
        #[Autowire(service: 'ai.vectorizer.default')] VectorizerInterface $vectorizer,
        Request $request
    ): JsonResponse {
        $query = $request->query->get('q', '');
        $limit = (int) $request->query->get('limit', 5);
        $minScore = (float) $request->query->get('min_score', 0.7);

        // Vectorize the query
        $vector = $vectorizer->vectorize($query, ['dimensions' => 1024]);

        // Query the store
        $results = $store->query(new VectorQuery($vector), [
            'limit' => $limit,
            'where' => "metadata->>'expert'=:expert",
            'params' => ['expert' => 'PHP']
        ]);

        // Filter by score and format results
        $data = [];
        foreach ($results as $result) {
            if ($result->getScore() >= $minScore) {
                $data[] = [
                    'id' => $result->id,
                    'score' => $result->getScore(),
                    'content' => $result->metadata['text'],
                    'expert' => $result->metadata['expert']
                ];
            }
        }

        return $this->json($data);
    }
}
```

### RAG (Retrieval-Augmented Generation) Example

```php
<?php

namespace App\Service;

use Symfony\AI\Chat\ChatInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\AI\Store\Query\VectorQuery;
use Symfony\AI\Store\Document\VectorizerInterface;
use Symfony\AI\Store\StoreInterface;

readonly class RagService
{
    public function __construct(
        private StoreInterface $store,
        private VectorizerInterface $vectorizer,
        private ChatInterface $chat,
    ) {}

    public function ask(string $question): string
    {
        // 1. Vectorize the question
        $vector = $this->vectorizer->vectorize($question);

        // 2. Retrieve relevant documents
        $results = $this->store->query(new VectorQuery($vector), [
            'limit' => 3
        ]);

        // 3. Build context from results
        $context = '';
        foreach ($results as $result) {
            if ($result->getScore() >= 0.7) {
                $context .= $result->metadata['content'] . "\n---\n";
            }
        }

        // 4. Create system prompt with context
        $systemPrompt = <<<PROMPT
You are a helpful assistant. Answer based ONLY on the context below.
If the answer is not in the context, say "I don't know."

Context:
$context
PROMPT;

        // 5. Initialize chat and submit
        $this->chat->initiate(new MessageBag(Message::forSystem($systemPrompt)));
        $response = $this->chat->submit(Message::ofUser($question));

        return $response->getContent();
    }
}
```

---

## Common Errors and Solutions

### Error 1: Missing Connection Configuration

```
Invalid configuration for path "ai.store.postgres.default": 
Either "dsn" or "dbal_connection" must be configured.
```

**Solution:** Add the `dbal_connection` to your configuration:

```yaml
ai:
    store:
        postgres:
            default:
                dbal_connection: 'doctrine.dbal.default_connection'
                table_name: 'product_store'
```

### Error 2: Wrong Query Type

```
StoreProxy::query(): Argument #1 ($query) must be of type 
Symfony\AI\Store\Query\QueryInterface, 
Symfony\AI\Platform\Vector\Vector given
```

**Solution:** Wrap the vector in a `VectorQuery`:

```php
// ❌ Wrong
$results = $store->query($vector, ['limit' => 5]);

// ✅ Correct
use Symfony\AI\Store\Query\VectorQuery;
$results = $store->query(new VectorQuery($vector), ['limit' => 5]);
```

### Error 3: SQL Syntax in WHERE Clause

```php
// ❌ Wrong (missing quotes around key)
'where' => "metadata->>expert=:expert'"

// ✅ Correct
'where' => "metadata->>'expert'=:expert"
```

### Error 4: pgvector Extension Not Enabled

```
SQLSTATE[42883]: Undefined function: ERROR: 
operator does not exist: vector <-> vector
```

**Solution:** Enable pgvector in PostgreSQL:

```sql
CREATE EXTENSION IF NOT EXISTS vector;
```

Or use a Docker image with pgvector pre-installed:

```yaml
image: pgvector/pgvector:pg16
```

---

## Best Practices

### 1. Use Doctrine Migrations for Production

Instead of `ai:store:setup`, create a migration:

```bash
php bin/console make:migration
```

### 2. Filter by Metadata

Always filter by metadata type when possible to improve query performance:

```php
$results = $store->query(new VectorQuery($vector), [
    'limit' => 5,
    'where' => "metadata->>'type'=:type",
    'params' => ['type' => 'product']
]);
```

### 3. Set Minimum Score Threshold

Filter low-confidence results:

```php
foreach ($results as $result) {
    if ($result->getScore() < 0.7) {
        continue;  // Skip low-confidence matches
    }
    // Process result
}
```

### 4. Use Appropriate Vector Dimensions

Match your embedding model's output dimensions:

```php
// For text-embedding-3-small
$vector = $vectorizer->vectorize($text, ['dimensions' => 1536]);

// For mxbai-embed-large
$vector = $vectorizer->vectorize($text, ['dimensions' => 1024]);
```

---

## Additional Resources

- [Symfony AI Documentation](https://symfony.com/doc/current/ai/index.html)
- [Symfony AI Store Documentation](https://symfony.com/doc/current/ai/components/store.html)
- [pgvector GitHub Repository](https://github.com/pgvector/pgvector)
- [Symfony AI PostgreSQL Store](https://github.com/symfony/ai-postgres-store)

---

*Generated from hands-on implementation experience with Symfony AI and PostgreSQL pgvector.*
