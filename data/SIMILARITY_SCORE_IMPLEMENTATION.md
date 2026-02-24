# Similarity Score Display Implementation

## Overview
Implemented display of cosine similarity scores in the product search results using a clean, entity-based approach.

## Solution Architecture

### 1. **Transient Property on Entity** ✅
Added a `similarityScore` property to the `Product` entity that is **not persisted** to the database:

```php
// src/Entity/Product.php
private ?float $similarityScore = null;

public function getSimilarityScore(): ?float
{
    return $this->similarityScore;
}

public function setSimilarityScore(?float $similarityScore): static
{
    $this->similarityScore = $similarityScore;
    return $this;
}
```

**Benefits:**
- ✅ No database schema changes required
- ✅ Score is temporary and only exists during the request
- ✅ Clean OOP approach

### 2. **Repository Enhancement** ✅
Modified `ProductRepository::searchByVector()` to set the score on each product:

```php
foreach ($results as $row) {
    $product = $this->find($row['id']);
    if ($product) {
        // Set the similarity score as a transient property
        $product->setSimilarityScore(round((float)$row['score'], 4));
        $products[] = $product;
    }
}
```

**Benefits:**
- ✅ Returns array of `Product` entities (consistent return type)
- ✅ No breaking changes to existing code
- ✅ Score is attached to the entity where it belongs

### 3. **Template Display** ✅
Display the score as a badge on each product card:

```twig
{% if product.similarityScore is not null %}
    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-sm font-bold text-gray-900">{{ (product.similarityScore * 100)|number_format(1) }}%</span>
        </div>
    </div>
{% endif %}
```

**Benefits:**
- ✅ Clean template code using `product.similarityScore`
- ✅ Conditional display (only shows if score exists)
- ✅ Formatted as percentage with 1 decimal place
- ✅ Beautiful UI with star icon and badge

## Why This Approach is Better

### ❌ Initial Approach (Not Ideal)
```php
// Returned mixed array structure
return [
    ['product' => $product, 'score' => 0.85],
    ['product' => $product, 'score' => 0.72],
];
```

**Problems:**
- Breaking change to return type
- Template complexity: `item.product.name` instead of `product.name`
- Inconsistent with other repository methods
- Harder to maintain

### ✅ Current Approach (Clean)
```php
// Returns Product entities with score attached
$product->setSimilarityScore(0.85);
return [$product1, $product2];
```

**Advantages:**
- Consistent return type: `Product[]`
- Clean template code: `product.similarityScore`
- No breaking changes
- Follows OOP principles
- Score is logically part of the product in this context

## Visual Result

Each product card now displays:
- **Top-right corner**: Similarity score badge (e.g., "85.3%")
- **Star icon**: Visual indicator of relevance
- **Glassmorphism design**: Modern, semi-transparent badge
- **Conditional display**: Only shows when score exists

## Score Interpretation

- **90-100%**: Highly relevant match
- **80-89%**: Very good match
- **70-79%**: Good match
- **60-69%**: Moderate match (minimum threshold)
- **< 60%**: Filtered out (not shown)

## Technical Details

### Transient Property
- Not mapped to database column
- Exists only in memory during request lifecycle
- Automatically null for products loaded outside vector search
- No performance impact on database operations

### Score Calculation
```sql
1 - (embedding <=> :embedding) as score
```
- Cosine distance operator: `<=>`
- Converted to similarity: `1 - distance`
- Range: 0.0 (no similarity) to 1.0 (identical)
- Rounded to 4 decimal places for display

### Filtering
```sql
WHERE 1 - (embedding <=> :embedding) > 0.6
```
- Only shows products with >60% similarity
- Reduces noise in search results
- Improves relevance of displayed items

## Future Enhancements

1. **Color-coded badges**: Different colors based on score ranges
2. **Sorting options**: Allow users to sort by relevance
3. **Score explanation**: Tooltip showing why item matched
4. **Adjustable threshold**: Let users control minimum similarity
5. **Debug mode**: Show raw cosine distance for testing
