# Product Data Setup

## Overview
Created separate data files with sample products for testing vector database functionality and semantic search capabilities.

## Data Files

### 1. Technology Products (`products.json`)
- **Location**: `/sfai/data/products.json`
- **Count**: 100 products
- **Categories**:
  - **Laptop** (15 products): Various brands including Dell, Apple, Lenovo, HP, ASUS, Microsoft, Acer, Razer, LG, MSI
  - **Computer** (10 products): Desktop computers including gaming PCs, business desktops, workstations
  - **Accessory** (75 products):
    - Peripherals (mice, keyboards)
    - Displays (monitors)
    - Audio (headphones, headsets, microphones)
    - Storage (SSDs, HDDs, external drives)
    - Connectivity (hubs, docks, adapters)
    - Power (UPS, PSUs)
    - Cooling (CPU coolers, AIOs)
    - Components (RAM, GPUs, CPUs, motherboards)
    - Cases
    - Laptop accessories (stands, sleeves, cooling pads, chargers)
    - Workspace accessories (desk mats, LED strips, cable management)

### 2. Clothing Products (`clothing.json`)
- **Location**: `/sfai/data/clothing.json`
- **Count**: 100 products
- **Categories**:
  - **Men** (25 products): T-shirts, dress shirts, jeans, suits, jackets, sweaters, pants, hoodies, athletic wear
  - **Women** (75 products): Dresses, jeans, blouses, leggings, sweaters, skirts, jackets, tops, athletic wear, formal wear

### 3. Grocery & Daily Commodities (`groceries.json`)
- **Location**: `/sfai/data/groceries.json`
- **Count**: 100 products
- **Categories**:
  - **Grains** (10 products): Basmati rice, brown rice, jasmine rice, wheat flour, oats, quinoa, semolina
  - **Pulses** (6 products): Red lentils, chickpeas, kidney beans, moong dal, urad dal, chana dal
  - **Spices & Seasonings** (13 products): Salt varieties, turmeric, cumin, coriander, chili powder, garam masala, pepper, cinnamon, cardamom
  - **Oils** (5 products): Olive oil, coconut oil, sunflower oil, mustard oil, sesame oil
  - **Dairy** (5 products): Milk, yogurt, cheese, butter
  - **Dairy Alternatives** (1 product): Almond milk
  - **Eggs** (2 products): Free-range, organic
  - **Pasta & Noodles** (5 products): Penne, spaghetti, instant noodles, rice noodles, vermicelli
  - **Condiments** (5 products): Ketchup, soy sauce, mayonnaise, hot sauce, mustard
  - **Spreads** (3 products): Peanut butter, jam, chocolate hazelnut spread
  - **Beverages** (9 products): Tea, coffee, cocoa, juices, coconut water, sparkling water
  - **Baking** (5 products): Cocoa powder, baking powder, baking soda, vanilla extract, cornstarch
  - **Nuts & Seeds** (7 products): Almonds, cashews, walnuts, peanuts, chia seeds, flax seeds, sunflower seeds
  - **Dried Fruits** (4 products): Raisins, dates, apricots, cranberries
  - **Canned Goods** (6 products): Tomato puree, coconut milk, canned beans, corn, tuna
  - **Vinegars** (3 products): Apple cider, white, balsamic
  - **Snacks** (6 products): Popcorn, chips, trail mix, chocolate, granola bars
  - **Sweeteners** (5 products): White sugar, brown sugar, honey, jaggery, maple syrup

### 4. Lifestyle & Romance (`lifestyle.json`)
- **Location**: `/sfai/data/lifestyle.json`
- **Count**: 100 products
- **Categories**:
  - **Romance & Ambiance** (13 products): Candles, diffusers, fairy lights, salt lamps, scented items, projectors
  - **Wellness & Intimacy** (18 products): Massage oils, spa products, yoga mats, meditation items, bath products, skincare
  - **Sleep & Comfort** (16 products): Luxury bedding, robes, pillows, silk items, blankets, pajamas, slippers
  - **Celebration & Dining** (12 products): Champagne flutes, fondue sets, wine accessories, gourmet foods, cookbooks
  - **Entertainment & Leisure** (21 products): Games, classes, experiences, outdoor activities, creative kits, adventures
  - **Memories & Keepsakes** (10 products): Photo albums, journals, personalized gifts, custom prints, memory boxes


## Product Data Structure
Each product contains:
```json
{
  "name": "Product Name",
  "description": "Detailed description with specifications and use cases",
  "price": "999.99",
  "category": "Laptop|Computer|Accessory|Men|Women",
  "tags": ["tag1", "tag2", "tag3"],
  "inStock": true
}
```

## Command Updates
Updated `LoadSampleDataCommand` to:
1. Read products from JSON files instead of hardcoded arrays
2. Include proper error handling for missing or invalid JSON
3. Support optional fields with fallback values
4. Maintain embedding generation for vector search

## Usage
Run the command to load all products into the database:
```bash
php bin/console app:load-sample-data
```

## Testing Vector DB Cosine Distance

The clothing dataset is specifically designed to test semantic search and cosine distance calculations. Here are example queries to test:

### Semantic Similarity Tests

#### 1. **Formal Wear Queries**
- Query: "professional office attire"
  - Should match: Men's suits, dress shirts, women's blazers, pencil skirts, sheath dresses
  - Tests: Business/formal semantic understanding

- Query: "elegant evening outfit"
  - Should match: Little black dress, cocktail dresses, silk blouses, dress pants
  - Tests: Formal occasion context

#### 2. **Casual Wear Queries**
- Query: "comfortable weekend clothes"
  - Should match: Hoodies, joggers, t-shirts, jeans, sweatpants
  - Tests: Casual/comfort semantic grouping

- Query: "relaxed everyday outfit"
  - Should match: Casual shirts, denim, comfortable pants, basic tees
  - Tests: Everyday wear context

#### 3. **Athletic Wear Queries**
- Query: "workout gear for gym"
  - Should match: Compression shirts, sports bras, athletic shorts, track pants, yoga leggings
  - Tests: Athletic/performance context

- Query: "running clothes"
  - Should match: Running shorts, compression shirts, athletic leggings, moisture-wicking items
  - Tests: Specific activity matching

#### 4. **Seasonal Queries**
- Query: "warm winter clothing"
  - Should match: Parkas, puffer jackets, wool sweaters, peacoats, turtlenecks
  - Tests: Seasonal/temperature context

- Query: "light summer outfits"
  - Should match: Linen shirts, tank tops, shorts, maxi dresses, breathable fabrics
  - Tests: Season-specific materials

#### 5. **Material/Fabric Queries**
- Query: "luxury soft fabrics"
  - Should match: Cashmere sweaters, silk blouses, merino wool items
  - Tests: Material quality understanding

- Query: "breathable natural fibers"
  - Should match: Cotton tees, linen pants, linen shirts
  - Tests: Material property matching

#### 6. **Style Queries**
- Query: "edgy fashionable outfit"
  - Should match: Leather jackets, leather leggings, bomber jackets, distressed jeans
  - Tests: Style/aesthetic understanding

- Query: "classic timeless pieces"
  - Should match: Oxford shirts, peacoats, little black dress, trench coats
  - Tests: Style longevity concept

#### 7. **Cross-Category Tests**
- Query: "something to wear to a wedding"
  - Should match: Suits, cocktail dresses, formal dresses, dress shirts, blazers
  - Tests: Event-based context across categories

- Query: "clothes for a beach vacation"
  - Should match: Swim trunks, maxi dresses, linen shirts, shorts, rompers
  - Tests: Location/activity context

#### 8. **Fit/Silhouette Queries**
- Query: "fitted body-hugging clothes"
  - Should match: Skinny jeans, bodysuits, compression shirts, pencil skirts
  - Tests: Fit description understanding

- Query: "loose flowing garments"
  - Should match: Wide-leg pants, palazzo pants, maxi dresses, oversized hoodies
  - Tests: Opposite fit characteristics

#### 9. **Grocery & Cooking Queries**
- Query: "ingredients for Indian curry"
  - Should match: Turmeric, cumin, coriander, garam masala, rice, lentils, coconut milk
  - Tests: Cuisine-specific ingredient understanding

- Query: "healthy breakfast options"
  - Should match: Oats, eggs, yogurt, honey, granola bars, almond milk, whole wheat flour
  - Tests: Meal context and health association

- Query: "baking essentials"
  - Should match: Flour, sugar, baking powder, baking soda, vanilla extract, butter, eggs
  - Tests: Activity-based ingredient grouping

- Query: "protein-rich foods"
  - Should match: Lentils, chickpeas, eggs, nuts, quinoa, tuna, Greek yogurt
  - Tests: Nutritional property understanding

- Query: "natural sweeteners"
  - Should match: Honey, maple syrup, jaggery, dates
  - Tests: Category understanding with natural/organic context

#### 10. **Cross-Domain Tests**
- Query: "organic healthy products"
  - Should match: Organic rice, organic eggs, organic honey, chia seeds, quinoa, almond milk
  - Tests: Cross-category health/organic understanding

- Query: "quick convenient meals"
  - Should match: Instant noodles, canned goods, pasta, ready-to-eat items
  - Tests: Convenience and time-saving context

### Expected Cosine Distance Behavior

**High Similarity (Distance < 0.3)**
- Items in same category with similar purpose
- Example: "Sports bra" and "Yoga leggings" for query "workout clothes"
- Example: "Basmati rice" and "Jasmine rice" for query "aromatic rice"

**Medium Similarity (Distance 0.3-0.6)**
- Related items across categories
- Example: "Dress shirt" and "Chinos" for query "business casual"
- Example: "Flour" and "Baking powder" for query "baking ingredients"

**Low Similarity (Distance > 0.6)**
- Unrelated items
- Example: "Swim trunks" and "Winter parka" for query "summer clothes"
- Example: "Laptop" and "Rice" for query "daily essentials"

### Testing Strategy

1. **Load all datasets** (products.json, clothing.json, and groceries.json)
2. **Run semantic searches** with the queries above
3. **Analyze cosine distances** to verify semantic understanding
4. **Compare results** between similar queries to test consistency
5. **Test cross-domain** queries (e.g., "organic items" should match across tech, clothing, and groceries)
6. **Test domain isolation** (e.g., "laptop" should not match groceries)

## Benefits
- **Separation of Concerns**: Data is separated from code
- **Easy Maintenance**: Update products by editing JSON files
- **Scalability**: Easy to add more products without touching code
- **Realistic Dataset**: 1000 diverse products across 10 distinct domains
- **Comprehensive Coverage**: Includes budget to premium items with realistic pricing
- **Semantic Diversity**: Rich descriptions for testing vector search quality
- **Cross-Domain Testing**: Test semantic understanding across completely different product categories
- **Real-World Scenarios**: Authentic shopping queries and use cases

## Complete Dataset Overview

### Product Catalog
- **Total Products:** 2,000+
- **Total Domains:** 15+
- **Key Categories:**
  - **Technology:** Laptops, Phones, Smart Devices
  - **Clothing:** Fashion, Outerwear, Seasonal
  - **Shoes:** Sports, Formal, Kids, Luxury
  - **Toys:** STEM, Creative, Active Play
  - **Wellness:** Personal Care, Health, Intimacy
  - **Groceries:** Pantry, Fresh, Essentials
  - **Home & Lifestyle:** Decor, Tools, Kitchen
  - **Pet & Outdoor:** Specialized gear and essentials
  - **Mass Variety:** Mixed datasets for comprehensive search testing

All products are stored as JSON files with pre-defined structures and are loaded into the database with their respective vector embeddings.
6. **Home Essentials** (100 products): Cleaning, kitchen, home tools, office supplies
7. **Personal Care** (100 products): Hygiene, skincare, medical supplies, fitness
8. **Baby & Kids** (100 products): Baby care, toys, learning, safety gear
9. **Pet Essentials** (100 products): Food, toys, grooming, furniture
10. **Outdoor & Auto** (100 products): Garden tools, car maintenance, outdoor leisure

### Cross-Domain Testing
- **Domain Isolation**: "laptop" should only match tech, not groceries
- **Shared Attributes**: "organic", "luxury", "portable", "essential" span multiple domains
- **Contextual Understanding**: "romantic getaway" matches lifestyle + travel
- **Price Tiers**: Budget to luxury across all categories

See `COMPLETE_DATASET_SUMMARY.md` for comprehensive testing strategies.
