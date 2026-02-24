# Vector Search Testing & Evaluation Guideline (Scale: 2,000+ Products)

## 1. Overview
With a dataset of over 2,000 products across 15+ domains, testing shifts from simple keyword validation to **Semantic Precision** and **Domain Nuance**. This guideline provides a structured approach to evaluating the vector search performance of the SFAI system.

---

## 2. Quantitative Testing (Performance)
### 2.1 Latency Benchmarking
Measure the time taken from query submission to result delivery.
- **Target**: < 500ms for 2,000 products.
- **Observation**: Monitor the `searchTime` badge in the UI.
- **Scale Test**: Compare latency of searching 100 products vs 2,000 products.

### 2.2 Precision at K (P@K)
For a specific query, how many of the top `K` (e.g., K=5) results are actually relevant?
- **Formula**: `(Relevant Items in Top K) / K`
- **Pass Criteria**: P@5 > 0.8 (at least 4 out of 5 are high relevance matches).

---

## 3. Qualitative Testing (Semantic Scenarios)

### 3.1 Scenario: Cross-Domain Intent
Test how the system maps a single intent across different product categories.
- **Query**: "Preparing for a rainy camping trip"
- **Expected Results**:
  - `Clothing`: Waterproof Raincoat, Gortex Parka.
  - `Shoes`: Rubber Rain Boots, Waterproof Trail Boots.
  - `Travel`: Waterproof Backpack Cover, 4-Season Tent.
  - `Outdoor`: Heavy-duty Wellingtons.
- **Validation**: Does the system pull from at least 3 different categories?

### 3.2 Scenario: Synonym & Concept Mapping
Test concept understanding without exact keyword overlap.
- **Query**: "Sustainable footwear for the office"
- **Expected Results**:
  - `Shoes`: All-Day Work Oxford (comfort focus).
  - `Shoes`: Orthotic Comfort Loafer.
  - `Shoes`: Recycled Knit Sneaker (sustainable/office crossover).
- **Validation**: Does it prioritize "Recycled" and "Comfort" over just "Office"?

### 3.3 Scenario: Problem-Solution Matching
Test if the system can act as a "Consultant".
- **Query**: "I have trouble sleeping and my back hurts"
- **Expected Results**:
  - `Lifestyle`: Weighted Sleep Mask, Silk Pillowcase.
  - `Health`: Smart Posture Tracker, Posture Correction Back Stool.
  - `Home`: Memory Foam Rug (comfort), Heated Blanket.
- **Validation**: Does it surface items from both "Wellness" and "Furniture/Office"?

---

## 4. Nuance & Edge Case Testing

### 4.1 Keyword Ambiguity (The "Protection" Test)
"Protection" means different things in different contexts.
- **Query**: "Personal Protection"
- **Expected Mix**:
  - `Wellness`: Condoms (intimacy protection).
  - `Health`: First Aid Kit, Hand Sanitizer.
  - `Clothing`: Reflective Safety Vest.
  - `Shoes`: Steel-Toe Safety Trainer.
- **Goal**: Check how the vector space clusters these different meanings.

### 4.2 Material & Property focus
- **Query**: "Breathable linen" vs "Warm Wool"
- **Validation**: Ensure "Breathable linen" surfaces Summer Dresses, Linen Shirts, and Huarache sandals, while "Warm Wool" surfaces Parkas, Beanies, and Winter Slippers.

---

## 5. Similarity Score Benchmarking
Using the transients `similarityScore` added to the `Product` entity:

| Query Type | Expected High Score (90%+) | Expected Med Score (75-85%) | Expected Low Score (60-70%) |
| :--- | :--- | :--- | :--- |
| **Exact Category** | Direct matches (e.g., "RTX 4090") | Related items (e.g., "Gaming Mouse") | Non-tech accessories |
| **Complex Intent** | Direct solutions (e.g., "Raincoat") | Contextual items (e.g., "Boots") | Distant associations (e.g., "Towel") |
| **Abstract Style** | Stylized items (e.g., "Leather Jacket") | Similar vibe (e.g., "Boots") | Neutral basics (e.g., "T-shirt") |

---

## 6. Regression Testing Workflow
1. **Load Data**: `php bin/console app:load-sample-data`
2. **Standard Query Set**: Execute the "Top 10 Benchmark Queries" defined in `COMPLETE_DATASET_SUMMARY.md`.
3. **Verify Badges**: Ensure the `similarityScore %` is displaying correctly on all cards.
4. **Category Filter**: Apply a category filter (e.g., "Shoes") and verify that non-category items are 100% excluded.

---

## 7. Troubleshooting Relevance
If you find "odd" results:
1. **Check Description**: Is the description too generic? (Vectors rely on descriptive text).
2. **Check Tags**: Ensure tags aren't over-powering the primary description.
3. **Task Type**: Verify the `EmbeddingService` is using `RETRIEVAL_DOCUMENT` for products and `RETRIEVAL_QUERY` for search input.
