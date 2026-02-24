# Vector Database Testing Guide

## Quick Start

### 1. Load Sample Data
```bash
php bin/console app:load-sample-data
```

This will load:
- 100 technology products (computers, laptops, accessories)
- 100 clothing items (men's and women's apparel)
- Sample articles

## Testing Cosine Distance

### Understanding Cosine Distance
- **0.0** = Identical vectors (perfect match)
- **< 0.3** = High similarity (very related items)
- **0.3-0.6** = Medium similarity (somewhat related)
- **> 0.6** = Low similarity (unrelated items)
- **1.0** = Completely opposite vectors

### Quick Test Queries

#### Test 1: Exact Category Match
**Query**: "laptop for programming"
**Expected High Matches** (distance < 0.3):
- MacBook Pro 14" M3
- Dell XPS 15 Laptop
- Lenovo ThinkPad X1 Carbon
- MSI Creator Z16

**Why**: Direct semantic match with "laptop" and "programming/development" context

---

#### Test 2: Semantic Understanding
**Query**: "comfortable work from home setup"
**Expected High Matches** (distance < 0.3):
- Ergonomic Office Chair (from products.json)
- Laptop Stand Aluminum
- Desk Mat XXL
- Ergonomic Wrist Rest Set

**Expected Medium Matches** (distance 0.3-0.6):
- Men's Hoodie Sweatshirt
- Women's Joggers
- Comfortable sweatpants

**Why**: Tests cross-domain semantic understanding of "comfort" and "work from home"

---

#### Test 3: Activity-Based Search
**Query**: "outfit for running marathon"
**Expected High Matches** (distance < 0.3):
- Men's Athletic Running Shorts
- Men's Compression Shirt
- Women's Yoga Leggings
- Women's Sports Bra

**Expected Low Matches** (distance > 0.6):
- Men's Wool Blend Suit Jacket
- Women's Silk Blouse
- Laptop accessories

**Why**: Tests activity context understanding

---

#### Test 4: Material Properties
**Query**: "breathable fabric for hot weather"
**Expected High Matches** (distance < 0.3):
- Men's Linen Shirt
- Women's Linen Pants
- Men's Cotton T-Shirt
- Women's Cotton T-Shirt
- Women's Tank Top

**Expected Low Matches** (distance > 0.6):
- Men's Winter Parka
- Women's Puffer Jacket
- Wool sweaters

**Why**: Tests material property and seasonal understanding

---

#### Test 5: Formal Occasion
**Query**: "professional business meeting attire"
**Expected High Matches** (distance < 0.3):
- Men's Wool Blend Suit Jacket
- Men's Slim Fit Dress Shirt
- Women's Blazer
- Women's Pencil Skirt
- Women's Sheath Dress

**Expected Low Matches** (distance > 0.6):
- Men's Swim Trunks
- Women's Romper
- Athletic wear

**Why**: Tests formal/professional context

---

#### Test 6: Price Range Semantic
**Query**: "luxury premium quality items"
**Expected High Matches** (distance < 0.3):
- Men's Cashmere Sweater ($179.99)
- Women's Cashmere Cardigan ($159.99)
- Men's Leather Jacket ($399.99)
- Women's Silk Blouse ($89.99)
- NVIDIA GeForce RTX 4090 ($1599.99)

**Why**: Tests quality/luxury semantic understanding

---

#### Test 7: Cross-Domain Comfort
**Query**: "comfortable items for long hours"
**Expected High Matches** (distance < 0.3):
- Ergonomic Office Chair
- Men's Hoodie Sweatshirt
- Women's Sweatpants
- Ergonomic Wrist Rest Set
- Women's Joggers

**Why**: Tests "comfort" concept across different product domains

---

#### Test 8: Style Aesthetic
**Query**: "edgy cool street style"
**Expected High Matches** (distance < 0.3):
- Men's Leather Jacket
- Women's Leather Leggings
- Men's Bomber Jacket
- Women's Denim Jacket
- Men's Denim Jeans

**Expected Low Matches** (distance > 0.6):
- Men's Wool Blend Suit
- Women's Pencil Skirt
- Formal dresses

**Why**: Tests style/aesthetic understanding

---

#### Test 9: Season-Specific
**Query**: "warm cozy winter clothes"
**Expected High Matches** (distance < 0.3):
- Men's Winter Parka
- Women's Puffer Jacket
- Men's Cashmere Sweater
- Women's Turtleneck Sweater
- Men's Peacoat

**Expected Low Matches** (distance > 0.6):
- Men's Swim Trunks
- Women's Tank Top
- Shorts

**Why**: Tests seasonal context and temperature association

---

#### Test 10: Technology Performance
**Query**: "high performance gaming equipment"
**Expected High Matches** (distance < 0.3):
- NVIDIA GeForce RTX 4090
- AMD Radeon RX 7900 XTX
- Intel Core i9-14900K
- Custom Gaming PC - RTX 4090
- ASUS ROG Strix G10

**Expected Low Matches** (distance > 0.6):
- Clothing items
- Office accessories

**Why**: Tests domain-specific technical understanding

---

## Advanced Testing Scenarios

### Scenario 1: Synonym Understanding
Test if the vector DB understands synonyms:
- "athletic wear" vs "sports clothes" vs "workout gear"
- "formal attire" vs "business clothes" vs "professional outfit"
- "computer" vs "PC" vs "desktop machine"

### Scenario 2: Negation (Challenging)
Test queries with negation:
- "casual clothes not for sports" (should exclude athletic wear)
- "laptop but not for gaming" (should exclude gaming laptops)

### Scenario 3: Multi-Attribute
Test complex multi-attribute queries:
- "affordable casual summer clothes" (price + style + season)
- "premium professional laptop for developers" (quality + purpose + user)

### Scenario 4: Contextual Inference
Test inference capabilities:
- "beach vacation essentials" (should infer: swimwear, light clothes, casual)
- "winter office outfit" (should infer: formal + warm)

## Measuring Success

### Good Vector Search Results:
✅ Top 5 results are semantically relevant
✅ Cosine distances correlate with semantic similarity
✅ Cross-domain queries work (e.g., "comfortable" matches both tech and clothing)
✅ Synonym queries return similar results
✅ Seasonal/contextual understanding is accurate

### Poor Vector Search Results:
❌ Random or unrelated items in top results
❌ Cosine distances don't correlate with relevance
❌ Exact keyword matching only (no semantic understanding)
❌ Synonyms return completely different results
❌ No contextual understanding

## Sample API Test

If you have an API endpoint for vector search:

```bash
# Test semantic search
curl -X POST http://localhost:8000/api/search \
  -H "Content-Type: application/json" \
  -d '{"query": "comfortable work from home setup", "limit": 10}'

# Expected response should include cosine distances
# Example:
# {
#   "results": [
#     {"name": "Ergonomic Office Chair", "distance": 0.15, "category": "Accessory"},
#     {"name": "Laptop Stand Aluminum", "distance": 0.22, "category": "Accessory"},
#     {"name": "Men's Hoodie Sweatshirt", "distance": 0.35, "category": "Men"}
#   ]
# }
```

## Troubleshooting

### All distances are similar (0.5-0.6)
**Problem**: Embeddings might not be diverse enough
**Solution**: Check if embedding model is working correctly

### Exact keyword matches only
**Problem**: Not using semantic embeddings
**Solution**: Verify embeddings are being generated and used in search

### Inconsistent results
**Problem**: Embedding generation might be non-deterministic
**Solution**: Check embedding service configuration

### Cross-domain queries fail
**Problem**: Embeddings might be too domain-specific
**Solution**: Ensure diverse training data or use general-purpose embedding model
