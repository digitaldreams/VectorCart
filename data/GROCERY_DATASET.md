# Grocery Dataset - Daily Commodities

## Overview
Created 100 grocery and daily commodity products to test vector database semantic search across a completely different domain from technology and clothing.

## Dataset Composition

### Complete Category Breakdown

1. **Grains (10 products)**
   - Rice varieties: Basmati, Brown, Jasmine, Sona Masoori, Wild Rice Mix
   - Flour: Whole wheat, All-purpose
   - Others: Semolina, Rolled oats, Quinoa

2. **Pulses (6 products)**
   - Lentils: Red (Masoor), Black (Urad), Green (Moong)
   - Beans: Kidney beans, Chickpeas (Kabuli Chana)
   - Split peas: Chana Dal

3. **Spices & Seasonings (13 products)**
   - Salts: Himalayan pink, Sea salt, Iodized, Black salt
   - Ground spices: Turmeric, Coriander, Red chili, Garam masala, Garlic powder, Onion powder
   - Whole spices: Cumin seeds, Black pepper, Cinnamon sticks, Cardamom pods, Bay leaves

4. **Oils (5 products)**
   - Extra virgin olive oil, Virgin coconut oil, Sunflower oil, Mustard oil, Toasted sesame oil

5. **Dairy (5 products)**
   - Whole milk, Greek yogurt, Cheddar cheese, Unsalted butter

6. **Dairy Alternatives (1 product)**
   - Almond milk unsweetened

7. **Eggs (2 products)**
   - Free-range eggs, Organic eggs

8. **Pasta & Noodles (5 products)**
   - Penne pasta, Whole wheat spaghetti, Instant noodles, Rice noodles, Vermicelli

9. **Condiments (5 products)**
   - Tomato ketchup, Soy sauce, Mayonnaise, Hot sauce, Mustard sauce

10. **Spreads (3 products)**
    - Creamy peanut butter, Strawberry jam, Chocolate hazelnut spread

11. **Beverages (9 products)**
    - Tea: Green tea bags, Black tea loose leaf
    - Coffee: Arabica beans, Instant coffee
    - Others: Cocoa powder, Orange juice, Coconut water, Sparkling water lemon, Mineral water

12. **Baking (5 products)**
    - Cocoa powder, Baking powder, Baking soda, Pure vanilla extract, Cornstarch

13. **Nuts & Seeds (7 products)**
    - Nuts: Raw almonds, Roasted cashews, Walnut halves, Roasted peanuts
    - Seeds: Organic chia seeds, Ground flax seeds, Sunflower seeds

14. **Dried Fruits (4 products)**
    - Seedless raisins, Medjool dates, Dried apricots, Dried cranberries

15. **Canned Goods (6 products)**
    - Tomato puree, Coconut milk, Canned chickpeas, Canned kidney beans, Sweet corn, Tuna in water

16. **Vinegars (3 products)**
    - Apple cider vinegar, White vinegar, Balsamic vinegar

17. **Snacks (6 products)**
    - Popcorn kernels, Potato chips, Trail mix, Dark chocolate bar, Granola bars

18. **Sweeteners (5 products)**
    - White sugar, Brown sugar, Organic honey, Jaggery, Pure maple syrup

## Key Testing Features

### 1. Nutritional Properties
Products tagged with health attributes for testing:
- **Protein-rich**: Lentils, chickpeas, quinoa, nuts, eggs, tuna, Greek yogurt
- **High-fiber**: Brown rice, oats, whole wheat flour, lentils, chia seeds, flax seeds
- **Gluten-free**: Rice, quinoa, oats (certified), cornstarch
- **Organic**: Brown rice, quinoa, honey, eggs, chia seeds

### 2. Cuisine-Specific
Products grouped by culinary tradition:
- **Indian**: Basmati rice, various dals, garam masala, turmeric, mustard oil, jaggery
- **Asian**: Jasmine rice, soy sauce, sesame oil, rice noodles, instant noodles
- **Mediterranean**: Olive oil, balsamic vinegar, pasta
- **Western**: Maple syrup, peanut butter, granola bars

### 3. Meal Context
Products associated with specific meals:
- **Breakfast**: Oats, eggs, yogurt, honey, granola bars, coffee, tea
- **Baking**: Flour, sugar, baking powder, vanilla extract, butter, eggs
- **Cooking**: Oils, spices, rice, lentils, pasta
- **Snacking**: Nuts, dried fruits, chips, chocolate, trail mix

### 4. Processing Level
Products at different processing stages:
- **Raw/Unprocessed**: Raw almonds, whole spices, fresh eggs
- **Minimally Processed**: Brown rice, whole wheat flour, raw honey
- **Processed**: White flour, instant noodles, canned goods
- **Refined**: White sugar, refined oils

### 5. Price Range
- **Budget**: $1.99 - $3.99 (table salt, baking soda, canned goods)
- **Mid-range**: $4.99 - $8.99 (rice, lentils, nuts, oils)
- **Premium**: $11.99 - $16.99 (organic products, specialty items, premium oils)

## Semantic Search Test Cases

### Basic Grocery Queries

1. **"ingredients for making bread"**
   - Expected: Flour, yeast (if added), salt, sugar, oil
   - Tests: Recipe ingredient understanding

2. **"healthy breakfast"**
   - Expected: Oats, eggs, yogurt, honey, granola, whole wheat flour
   - Tests: Meal + health context

3. **"Indian spices"**
   - Expected: Turmeric, cumin, coriander, garam masala, cardamom
   - Tests: Cuisine-specific grouping

4. **"protein sources"**
   - Expected: Lentils, chickpeas, eggs, nuts, quinoa, tuna
   - Tests: Nutritional property matching

5. **"natural unprocessed foods"**
   - Expected: Brown rice, raw honey, raw nuts, whole spices, organic products
   - Tests: Processing level understanding

### Cross-Domain Queries

1. **"organic products"**
   - Expected: Organic rice, organic eggs, organic honey, chia seeds, quinoa
   - Tests: Attribute matching across categories

2. **"quick convenient items"**
   - Expected: Instant noodles, instant coffee, canned goods, ready-to-eat snacks
   - Tests: Convenience attribute

3. **"luxury premium items"**
   - Expected: Extra virgin olive oil, balsamic vinegar, Medjool dates, premium coffee
   - Tests: Quality/price tier understanding

### Nutritional Queries

1. **"high fiber foods"**
   - Expected: Brown rice, oats, lentils, chia seeds, flax seeds, whole wheat flour
   - Tests: Nutritional content understanding

2. **"heart healthy fats"**
   - Expected: Olive oil, nuts, avocado oil (if added), omega-3 rich items
   - Tests: Health benefit association

3. **"gluten free options"**
   - Expected: Rice varieties, quinoa, certified oats, cornstarch
   - Tests: Dietary restriction matching

### Cooking Context Queries

1. **"stir fry ingredients"**
   - Expected: Soy sauce, sesame oil, rice noodles, vegetables (if added)
   - Tests: Cooking method understanding

2. **"baking essentials"**
   - Expected: Flour, sugar, baking powder, vanilla extract, butter, eggs
   - Tests: Activity-based grouping

3. **"salad dressing ingredients"**
   - Expected: Olive oil, vinegars, mustard, honey
   - Tests: Recipe component understanding

## Expected Semantic Patterns

### High Similarity (Score > 85%)
- Rice varieties for "rice" query
- Different lentils for "dal" query
- Salt varieties for "salt" query
- Oil types for "cooking oil" query

### Medium Similarity (Score 70-85%)
- Flour and baking powder for "baking" query
- Rice and lentils for "Indian staples" query
- Nuts and seeds for "healthy snacks" query

### Low Similarity (Score < 70%)
- Sweet items vs savory items
- Breakfast items vs dinner items
- Raw ingredients vs processed snacks

## Cross-Domain Testing

### Should Match Across Domains
- **"organic"**: Organic rice, organic eggs, organic clothing (if available)
- **"natural"**: Natural honey, natural fibers (linen), natural products
- **"premium"**: Premium oils, premium coffee, premium laptops

### Should NOT Match Across Domains
- **"rice"**: Should only match food rice, not tech products
- **"laptop"**: Should only match computers, not groceries
- **"dress"**: Should only match clothing, not food items

## Integration with Existing Datasets

### Total Dataset: 300 Products
- 100 Technology products (computers, laptops, accessories)
- 100 Clothing products (men's and women's apparel)
- 100 Grocery products (daily commodities)

### Testing Strategy
1. **Domain Isolation**: Verify tech queries don't match groceries
2. **Cross-Domain Attributes**: Test "organic", "premium", "convenient" across all domains
3. **Semantic Precision**: Ensure "rice" matches food, not "price" in tech products
4. **Contextual Understanding**: "healthy" should match food and athletic wear, not electronics

## Use Cases

### 1. E-commerce Search
Test realistic grocery shopping queries:
- "What do I need for pasta dinner?"
- "Ingredients for chocolate cake"
- "Healthy snacks for kids"

### 2. Recipe Assistance
Test ingredient discovery:
- "Spices for curry"
- "Baking ingredients"
- "Salad toppings"

### 3. Dietary Filters
Test restriction matching:
- "Gluten-free grains"
- "Vegan protein sources"
- "Sugar-free sweeteners"

### 4. Meal Planning
Test meal context:
- "Quick breakfast options"
- "Lunch box ideas"
- "Dinner staples"

## Benefits for Vector DB Testing

1. **Completely Different Domain**: Tests if embeddings can handle diverse product types
2. **Rich Nutritional Context**: Tests understanding of health and dietary concepts
3. **Cuisine Associations**: Tests cultural and regional food understanding
4. **Processing Levels**: Tests differentiation between raw, processed, and refined
5. **Meal Context**: Tests temporal and usage context understanding
6. **Cross-Domain Validation**: Ensures domain isolation while allowing attribute matching
