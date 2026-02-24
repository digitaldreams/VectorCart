## Knowledgebase
**Vector**
A vector is just an array of numbers.

**Dimensions**  How many numbers in the array. (The length of array)

**Embedding**   Ho to get vector. Converting text to vector via specialized embedding model.
All vectors in your database table/collection must have same dimensions. Some common dimentions are 768,1536,3072.
That actually the number of item in an array. The more dimensions the better that make searching costly.

**Distance Matrix (Measuring Similarity)**
How do you know if two vectors are similar?
You measure the distance between them. This are mathmatic calculation

**Cosine Similarity**
Measure the angle between two vector.
when to use: Text embeddings, semantic search

**Euclidean Distance (L2)**
Straight line distance between two points.
when to use: Image embeddings, spatial data

**spatial data**: Spatial data, also known geospatial data or geographic informations refers to data that is related to Geographic location


**Semantic Search**
Semantic search is an advanced information retrival technology that focuses on understanding the meaning and context beind a user search query, rather that simply matching keywords.

**Indexing**
without an index, vector search is slow with an index it's fast.

**flat**: No index, brute force
**IVFFlaat**: Cluster based search
Query comes in -> Find nearest cluster(B) ->Search within cluster B only
Much faster than searching all vectors
Query tuning
SET ivfflat.probes=1 fast, 70% recall
                  =10 Medium 85% recall
                  =50 slow 95% recall

 Medium sepeed,good for memory constraint
lists=100 (number of clusters)
sqrt(total row) is good

**HNSQ** Best for most use cases.
Multi layer graph navigation
Imagine a city with highways,main roads and local streets

High speed + High recall = High Memory
m = 16 (connnections per layer 16,64) higher is better
ef_construction = 64 to 200 Build time search depth
ef_search = 50-200 highe is better

**IVF-PQ**: Extrememe compression for billions of vectors

## Repository Plan (Symfony 8)

### Entity
**User**
- id
- password
- email
- name
- role
- about

**Product**
- id
- title
- description
- image_path
- image_vector
- price
- tags (json)
- category
- info_vector ( it contains <title> <description> <tags> <category>)

**Cart**
- id
- user_id
- status

**CartItem**
- cart_id
- product_id
- total

**SearchQuery**
- id
- keyword
- vector
- user_id



Create a simple product table just to demonstrate our features


### Tasks
- Run schema:update
- Create admin panel for product list, create, use MapRequestPayload
- Create Product Service where
- Create a Background Job (Messenger event and listener) that will take the newly created product and generate the vectors and save it. make the status completed or Active
- Create a duplicate Finder Service where find duplicate product info and images using vector search. If similary is more than 85% then its duplicate item.
- Introduce Rate limiting and round robin in embedding. keep eembedding 1536 dimension and use Google
- Create product index and search page.
  - Need to convert user search query to Vector
  - Create PostgreSqlProductRepository::search method and use cosine distance matrix and only allow if similarity rate greather than 0.7
  - Create PostgreSqlProductRepository::searchByImage and use Euclidean distance matrix and allow if similary rate is greather than 0.5
  - create product/index.blade.php and search.blade.php .
  - allow image upload search by Image
  - Apply filters by category, price
- Create a Recommendation Service
  - AVG previous Search Query Vector
  - Find product purchased habit from Cart
  - Trending Products


## Draft article Outline and thinking

