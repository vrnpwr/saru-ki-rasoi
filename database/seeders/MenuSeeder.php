<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Image pools for each category
        $images = [
            'burger' => [
                'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1586190848861-99aa4a171e90?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1550547660-d9450f859349?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1603064752734-4c48eff53d05?fm=jpg&q=60&w=800',
            ],
            'pizza' => [
                'https://images.unsplash.com/photo-1513104890138-7c749659a591?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1593504049359-74330189a345?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1613564834361-9436948817d1?fm=jpg&q=60&w=800',
            ],
            'drink' => [
                'https://images.unsplash.com/photo-1619385006774-942adfe8dd6f?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1619385006891-11af4f36aeef?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1697029237968-aff8846b2d8d?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1681579289891-e9a7b1b0559c?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1681579289916-af960b852352?fm=jpg&q=60&w=800',
            ],
            'dessert' => [
                'https://images.unsplash.com/photo-1551024601-bec78aea704b?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1551024506-0bccd828d307?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1563805042-7684c019e1cb?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1587314168485-3236d6710814?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1558326567-98ae2405596b?fm=jpg&q=60&w=800',
            ],
            'salad' => [
                'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1607532941433-304659e8198a?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1505253716362-afaea1d3d1af?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1540420773420-3366772f4999?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?fm=jpg&q=60&w=800',
            ],
            'fries' => [
                'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1630431341973-02e1b662ec35?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1518013431117-eb1465fa5752?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1598679253544-2c97992403ea?fm=jpg&q=60&w=800',
                'https://images.unsplash.com/photo-1541592106381-b31e9677c0e5?fm=jpg&q=60&w=800',
            ],
        ];

        // Specific categories to create
        $categoriesData = [
            ['name' => 'Burgers', 'slug' => 'burgers', 'food_type' => 'burger'],
            ['name' => 'Pizza', 'slug' => 'pizza', 'food_type' => 'pizza'],
            ['name' => 'Drinks', 'slug' => 'drinks', 'food_type' => 'drink'],
            ['name' => 'Desserts', 'slug' => 'desserts', 'food_type' => 'dessert'],
            ['name' => 'Salads', 'slug' => 'salads', 'food_type' => 'salad'],
            ['name' => 'Sides', 'slug' => 'sides', 'food_type' => 'fries'],
        ];

        foreach ($categoriesData as $catData) {
            $category = Category::firstOrCreate(
                ['slug' => $catData['slug']],
                ['name' => $catData['name']]
            );

            // Create 15 items per category
            for ($i = 0; $i < 15; $i++) {
                // Get a random image from the category pool
                // We use array_rand or just modulo to cycle through them
                $imageKey = $catData['food_type'];
                $availableImages = $images[$imageKey] ?? [];

                $imageUrl = null;
                if (!empty($availableImages)) {
                    $imageUrl = $availableImages[$i % count($availableImages)];
                } else {
                    // Fallback just in case
                    $imageText = $catData['name'] . ' ' . ($i + 1);
                    $imageUrl = "https://placehold.co/600x400?text=" . urlencode($imageText);
                }

                $item = Item::create([
                    'category_id' => $category->id,
                    'name' => $this->getFoodName($catData['food_type'], $faker),
                    'description' => $faker->paragraph(2),
                    'price' => $faker->randomFloat(2, 5, 30), // Price between 5 and 30
                    'image_path' => $imageUrl,
                ]);

                // Add variations for Pizza (Size, Crust) and Burgers (Extra Cheese) randomly
                if ($catData['slug'] === 'pizza') {
                    $variation = \App\Models\ItemVariation::create([
                        'item_id' => $item->id,
                        'name' => 'Size',
                        'type' => 'radio',
                        'required' => true,
                    ]);

                    \App\Models\ItemVariationOption::create(['item_variation_id' => $variation->id, 'name' => 'Small', 'price' => 0]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $variation->id, 'name' => 'Medium', 'price' => 2.00]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $variation->id, 'name' => 'Large', 'price' => 4.00]);

                    $toppings = \App\Models\ItemVariation::create([
                        'item_id' => $item->id,
                        'name' => 'Extra Toppings',
                        'type' => 'checkbox',
                        'required' => false,
                    ]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $toppings->id, 'name' => 'Extra Cheese', 'price' => 1.50]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $toppings->id, 'name' => 'Mushrooms', 'price' => 1.00]);

                } elseif ($catData['slug'] === 'burgers' && $i % 2 == 0) {
                    $variation = \App\Models\ItemVariation::create([
                        'item_id' => $item->id,
                        'name' => 'Make it a Meal',
                        'type' => 'radio',
                        'required' => false,
                    ]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $variation->id, 'name' => 'Just Burger', 'price' => 0]);
                    \App\Models\ItemVariationOption::create(['item_variation_id' => $variation->id, 'name' => 'Meal (Fries + Drink)', 'price' => 3.50]);
                }
            }
        }
    }

    private function getFoodName($type, $faker)
    {
        $adjectives = ['Classic', 'Spicy', 'Crispy', 'Mega', 'Deluxe', 'Supreme', 'Cheesy', 'Double', 'Golden', 'Fresh'];
        $modifiers = ['Special', 'Delight', 'Bonanza', 'Feast', 'Combo', 'Surprise', 'Tower', 'Blast'];

        $adj = $faker->randomElement($adjectives);
        $mod = $faker->randomElement($modifiers);

        switch ($type) {
            case 'burger':
                return "$adj " . $faker->randomElement(['Beef', 'Chicken', 'Veggie', 'Bacon', 'Fish']) . " Burger";
            case 'pizza':
                return "$adj " . $faker->randomElement(['Pepperoni', 'Cheese', 'Veggie', 'Meat', 'Hawaiian']) . " Pizza";
            case 'drink':
                return "$adj " . $faker->randomElement(['Cola', 'Lemonade', 'Smoothie', 'Shake', 'Ice Tea']);
            case 'dessert':
                return "$adj " . $faker->randomElement(['Cake', 'Pie', 'Ice Cream', 'Pudding', 'Brownie']);
            case 'salad':
                return "$adj " . $faker->randomElement(['Caesar', 'Greek', 'Garden', 'Cobb', 'Spinach']) . " Salad";
            case 'fries':
                return "$adj " . $faker->randomElement(['Fries', 'Rings', 'Wedges', 'Tots', 'Chips']);
            default:
                return $faker->words(3, true);
        }
    }
}
