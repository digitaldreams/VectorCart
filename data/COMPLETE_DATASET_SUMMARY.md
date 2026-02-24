# Complete Dataset Summary - 2,000+ Products Across 15+ Domain Areas

## Overview
Created a massive product database with **over 2,000 products** across **more than 15 distinct domains/categories** (including specialized variety batches) for testing high-scale vector database semantic search, performance, and nuanced domain understanding.

## Dataset Breakdown

### 1-5. Core Domain Datasets (500 Products)
- **Technology** (100): Computers, laptops, peripherals, high-end components.
- **Clothing** (100): Men's and women's apparel (fashion, casual).
- **Groceries** (100): Fresh food, pantry staples, dairy, beverages.
- **Lifestyle & Romance** (100): Decor, wellness, couples activities, gifts.
- **Travel** (100): Luggage, gear, experiences, travel accessories.

### 6-10. Lifestyle & Essentials (500 Products)
- **Home Essentials** (100): Cleaning, tools, office supplies, kitchenware.
- **Personal Care & Health** (100): Hygiene, skincare, medical monitoring.
- **Baby & Kids** (100): Care products, learning toys, safety gear.
- **Pet Essentials** (100): Food, toys, grooming for various pets.
- **Outdoor & Automotive** (100): Garden tools, car care, leisure.

### 11-15. Specialized Variety Batches (1,000+ Products)
**Focus**: Deepening specific domains and providing mass data for performance testing.
- **Shoes Variety** (250+): Men's, Women's, Kids', Sports, Luxury, specialized (Hiking, Cycling, Safety).
- **Toys Variety** (250+): STEM, Active Play, Role-play, Learning, Electronic.
- **Wellness & Intimacy** (100+): Health gadgets, sexual wellness, safety products.
- **Clothing Outerwear** (100+): Jackets, parkas, rainwear, winter gear.
- **Mass Variety Batches**: Diverse mixed items for comprehensive semantic search validation.

---

## Detailed Category Mapping
| Category | Sub-categories / Focus | Key Files |
| :--- | :--- | :--- |
| **Shoes** | Formal, Athletic, Casual, Kids, Safety, Specialized | `shoes_men.json`, `shoes_women_kids.json`, `shoes_specialized.json`, `shoes_mass_variety.json` |
| **Clothing** | Fashion, Workwear, Outerwear, Winter, Retro | `clothing.json`, `clothing_outerwear.json`, `clothing_mass_variety.json` |
| **Wellness** | Skincare, Health Tech, Intimacy, Hygiene, Safety | `wellness_essentials.json`, `essentials_personal_care.json`, `personal_mass_variety.json` |
| **Toys** | STEM, Active, Imaginative, Baby, Advanced | `toys_advanced.json`, `essentials_baby_kids.json`, `toys_mass_variety.json` |
| **Home/Travel** | Office, Kitchen, Luggage, Expedition, Tools | `essentials_home.json`, `travel.json`, `essentials_outdoor_automotive.json` |

## Scale Analysis
- **Total Product Count**: ~2,070
- **Total Articles**: 5 (Vector DB, Productivity, Healthcare AI, Sustainability, ML Introduction)
- **Total Data Files**: 30+ JSON datasets

## Testing Capabilities
1. **High-Scale Performance**: Test search latency across 2,000+ high-dimensional vectors.
2. **Deep Domain Nuance**: Distinguish between "Running shoes" (Athletic) and "Running equipment" (Tech/Clothing).
3. **Cross-Domain Intent**: Map intent like "preparing for rain" to jackets, boots, and umbrellas.
4. **Abstract Concept Mapping**: Map "Eco-friendly" to products across 10+ different categories.

## Loading the Dataset
```bash
php bin/console app:load-sample-data
```
*Note: Due to the size (2000+ products) and embedding generation, this process may take 30-40 minutes (1s delay per product to respect API limits).*
