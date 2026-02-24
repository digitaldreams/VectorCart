# Clothing Dataset Summary

## Overview
Created 100 clothing products specifically designed to test vector database cosine distance and semantic search capabilities.

## Dataset Composition

### Men's Clothing (25 items)
1. **Casual Wear** (8 items)
   - T-shirts, polo shirts, henley shirts
   - Jeans (straight leg, denim)
   - Hoodies, flannel shirts

2. **Formal Wear** (5 items)
   - Dress shirts, oxford shirts
   - Suit jacket, chinos
   - Peacoat

3. **Athletic Wear** (4 items)
   - Running shorts, track pants
   - Compression shirt, swim trunks

4. **Outerwear** (5 items)
   - Leather jacket, bomber jacket
   - Winter parka, windbreaker
   - Quilted vest

5. **Specialty** (3 items)
   - Linen shirt, cargo pants
   - Cashmere sweater, turtleneck

### Women's Clothing (75 items)
1. **Dresses** (10 items)
   - Maxi, cocktail, wrap, sweater, shift, sheath, A-line dresses
   - Little black dress

2. **Tops** (15 items)
   - Blouses (silk), t-shirts, tank tops
   - Camisoles, tunics, bodysuits
   - Off-shoulder, halter, peplum tops

3. **Bottoms** (12 items)
   - Jeans (skinny, boyfriend)
   - Pants (wide-leg, palazzo, linen, cargo, culottes)
   - Skirts (pencil, midi, pleated)
   - Shorts, capris

4. **Athletic Wear** (6 items)
   - Yoga leggings, sports bra
   - Track jacket, joggers
   - Sweatpants

5. **Outerwear** (8 items)
   - Trench coat, blazer, denim jacket
   - Puffer jacket, fleece jacket
   - Cardigan sweaters

6. **Sweaters & Knitwear** (8 items)
   - Cashmere cardigan, turtleneck
   - Cropped sweater, cowl neck
   - Cardigan sweater

7. **One-Pieces** (5 items)
   - Jumpsuit, romper
   - Kimono robe

8. **Specialty** (11 items)
   - Leather leggings, hoodie
   - Button-down shirt, various specialized items

## Key Features for Testing

### 1. Semantic Diversity
Products are described with rich, varied language to test semantic understanding:
- **Synonyms**: "comfortable" vs "cozy" vs "relaxed"
- **Context**: "professional" vs "business" vs "office"
- **Materials**: "breathable" vs "lightweight" vs "cooling"

### 2. Category Overlap
Intentional overlap between categories for testing:
- Athletic wear that's also casual (joggers, hoodies)
- Business casual items (chinos, blazers)
- Seasonal crossover (light jackets, transitional pieces)

### 3. Price Range Variation
- **Budget**: $19.99 - $44.99 (basics, casual wear)
- **Mid-range**: $49.99 - $99.99 (quality everyday items)
- **Premium**: $129.99 - $399.99 (luxury materials, formal wear)

### 4. Seasonal Indicators
Clear seasonal language for testing temporal context:
- **Summer**: "breathable", "lightweight", "cooling", "beach"
- **Winter**: "warm", "insulated", "cozy", "cold weather"
- **Transitional**: "layering", "versatile", "all-season"

### 5. Occasion-Based
Products tagged for specific occasions:
- **Formal**: weddings, business meetings, evening events
- **Casual**: weekend, everyday, relaxed
- **Athletic**: gym, running, yoga, sports
- **Special**: beach, vacation, lounging

### 6. Fit & Silhouette Variety
Different fit descriptions for testing:
- **Fitted**: skinny, slim, tailored, body-hugging
- **Relaxed**: loose, flowing, oversized, comfortable
- **Structured**: tailored, professional, polished

## Testing Strategies

### Basic Semantic Tests
1. **Direct Match**: "women's dress" → should return dresses
2. **Synonym Match**: "ladies' frock" → should return dresses
3. **Context Match**: "wedding outfit" → should return formal wear

### Advanced Semantic Tests
1. **Multi-attribute**: "comfortable summer workout clothes"
2. **Negation**: "formal wear not for weddings"
3. **Inference**: "beach vacation essentials"

### Cross-Category Tests
1. **Material-based**: "breathable fabrics" (should match linen, cotton across categories)
2. **Occasion-based**: "office attire" (should match both men's and women's business wear)
3. **Season-based**: "winter warmth" (should match parkas, sweaters, coats)

### Distance Validation
1. **High similarity** (< 0.3): Items in same subcategory
   - Example: Sports bra + Yoga leggings
2. **Medium similarity** (0.3-0.6): Related categories
   - Example: Dress shirt + Suit jacket
3. **Low similarity** (> 0.6): Unrelated items
   - Example: Swim trunks + Winter parka

## Expected Cosine Distance Patterns

### Same Category, Same Purpose
- Distance: **0.05 - 0.20**
- Example: "Men's T-Shirt" vs "Women's T-Shirt" for query "casual cotton shirt"

### Same Category, Different Purpose
- Distance: **0.25 - 0.40**
- Example: "Athletic Shorts" vs "Dress Pants" for query "comfortable pants"

### Different Category, Related Purpose
- Distance: **0.30 - 0.50**
- Example: "Sports Bra" vs "Compression Shirt" for query "workout gear"

### Completely Unrelated
- Distance: **0.60 - 0.90**
- Example: "Wool Sweater" vs "Swim Trunks" for query "summer clothes"

## Quality Metrics

### Good Vector Search Performance
✅ **Precision**: Top 10 results are all relevant (>80%)
✅ **Semantic Understanding**: Synonyms return similar results
✅ **Context Awareness**: Occasion/season queries work correctly
✅ **Distance Correlation**: Cosine distance correlates with relevance
✅ **Cross-category**: Related items from different categories appear together

### Poor Vector Search Performance
❌ **Random Results**: Top results are unrelated to query
❌ **Keyword-only**: Only exact keyword matches, no semantic understanding
❌ **No Context**: Seasonal/occasion queries fail
❌ **Distance Mismatch**: Similar items have high distances
❌ **Category Silos**: Can't find related items across categories

## Sample Queries for Testing

### Easy (Should work well)
- "men's casual shirt"
- "women's formal dress"
- "athletic wear"

### Medium (Tests semantic understanding)
- "comfortable work from home outfit"
- "professional business meeting attire"
- "summer beach vacation clothes"

### Hard (Tests advanced semantics)
- "elegant evening outfit for special occasion"
- "breathable natural fabrics for hot weather"
- "edgy fashionable street style"

### Very Hard (Tests inference)
- "what to wear to a job interview"
- "outfit for outdoor wedding in fall"
- "clothes for yoga class in summer"

## Integration with Technology Products

The clothing dataset complements the technology products dataset (100 items) to enable:

1. **Cross-domain testing**: Query "comfortable items" should match both ergonomic chairs and casual clothing
2. **Domain specificity**: Query "gaming" should only match tech products
3. **Price comparison**: Test if "luxury" concept works across domains
4. **Material properties**: "breathable" should match linen clothes and mesh chairs

## Total Dataset: 200 Products
- 100 Technology products (computers, laptops, accessories)
- 100 Clothing products (men's and women's apparel)
- Rich semantic diversity for comprehensive vector search testing
