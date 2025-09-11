<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Bazaar;
use App\Models\Comment;
use App\Models\Image;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Make sure public storage symlink exists
        if (!file_exists(public_path('storage'))) {
            \Artisan::call('storage:link');
        }

        // Example Syrian cities
        $cities = [
            ['Damascus', '0111234567', 33.5138, 36.2765],
            ['Aleppo', '0219876543', 36.2021, 37.1343],
            ['Homs', '0314567890', 34.7324, 36.7131],
            ['Latakia', '0417654321', 35.5131, 35.7815],
        ];

        // Demo store images (put them under database/seeders/images/stores/)
        $storeImages = [
            'store1.jpeg',
            'store2.jpeg',
            'store3.jpeg',
            'store4.jpeg'
        ];

        // Demo product images (put them under database/seeders/images/products/)
        $productImages = [
            'product1.jpg',
            'product2.jpg',
            'product3.jpg',
            'product4.jpg',
            'product5.jpg',
            'product6.webp'
        ];

        $allUsers = collect();

        for ($i = 0; $i < 4; $i++) {
            // Create User
            $user = User::create([
                'name' => "User {$i}",
                'number' => '0999' . rand(100000, 999999),
                'gender' => rand(0, 1),
                'age' => rand(20, 40),
                'email' => "user{$i}@example.com",
                'password' => bcrypt('password'),
            ]);
            $allUsers->push($user);

            // Create Address for User
            [$city, $phone, $lat, $lng] = $cities[$i];
            $address = Address::create([
                'city' => $city,
                'phone_number' => $phone,
                'latitude' => $lat,
                'longitude' => $lng,
                'user_id' => $user->id,
                'label' => "Home Address {$i}"
            ]);

            // Create Store for User
            $store = Store::create([
                'name' => "Demo Store {$i}",
                'description' => "This is demo store {$i} located in {$city}.",
                'user_id' => $user->id,
                'store_category_id' => rand(1, 7), // assuming 7 seeded categories
                'location_type' => $i % 2 == 0 ? 'onsite' : 'online',
                'address_id' => $address->id,
                'rating' => rand(3, 5),
                'rating_count' => rand(5, 20),
            ]);

            // Attach image to store
            $storeImageFile = $storeImages[$i % count($storeImages)];
            $this->attachImage($store, "stores/{$storeImageFile}");

            $hiddenAssigned = false;
            // Create Products for User
            for ($p = 0; $p < 6; $p++) {
                // allow hiding at most one product
                $showInStore = true;
                if (!$hiddenAssigned && rand(0, 5) === 0) {
                    $showInStore = false;
                    $hiddenAssigned = true;
                }
                $product = Product::create([
                    'user_id' => $user->id,
                    'name' => "Product {$i}-{$p}",
                    'description' => "This is demo product {$p} for store {$i}.",
                    'price' => rand(10, 200),
                    'cost' => rand(5, 100),
                    'stock_qty' => rand(10, 50),
                    'show_in_store' => $showInStore,
                    'rating' => rand(1, 5),
                    'rating_count' => rand(0, 50),
                    'product_category_id' => rand(1, 15), // assuming 15 seeded categories
                ]);

                // Attach random product image
                $prodImageFile = $productImages[array_rand($productImages)];
                $this->attachImage($product, "products/{$prodImageFile}");
                // Add comments to product
                $this->addCommentsAndUpdateRating($product, $allUsers);
            }

            // Add comments to store
            $this->addCommentsAndUpdateRating($store, $allUsers);
        }
        $allProducts = Product::all();
        $allUsers = User::all();

        $bazaarImages = [
            'bazaar1.jpeg',
            'bazaar2.jpeg',
            'bazaar3.jpeg'
        ];

        $now = CarbonImmutable::now();

        // Past Bazaar
        $pastBazaar = Bazaar::create([
            'name' => "Past Bazaar",
            'description' => "This is a past bazaar with expired dates.",
            'start_date' => $now->subDays(10),
            'end_date' => $now->subDays(5),
            'start_requesting_date' => $now->subDays(15),
            'end_requesting_date' => $now->subDays(11),
            'user_id' => $allUsers->random()->id,
            'address_id' => Address::inRandomOrder()->first()->id,
            'location_type' => 'onsite',
            'category_id' => 1, // بافتراض عندك bazaar_categories seeded
        ]);
        $this->attachImage($pastBazaar, "bazaars/{$bazaarImages[0]}");
        $pastBazaar->products()->attach($allProducts->random(5)->pluck('id')->toArray());
        $this->addCommentsAndUpdateRating($pastBazaar, $allUsers);

        // Ongoing Bazaar
        $ongoingBazaar = Bazaar::create([
            'name' => "Ongoing Bazaar",
            'description' => "This bazaar is currently active.",
            'start_date' => $now->subDays(2),
            'end_date' => $now->addDays(3),
            'start_requesting_date' => $now->subDays(5),
            'end_requesting_date' => $now->subDays(1),
            'user_id' => $allUsers->random()->id,
            'address_id' => Address::inRandomOrder()->first()->id,
            'location_type' => 'online',
            'category_id' => 2,
        ]);
        $this->attachImage($ongoingBazaar, "bazaars/{$bazaarImages[1]}");
        $ongoingBazaar->products()->attach($allProducts->random(6)->pluck('id')->toArray());
        $this->addCommentsAndUpdateRating($ongoingBazaar, $allUsers);

        // Upcoming Bazaar
        $upcomingBazaar = Bazaar::create([
            'name' => "Upcoming Bazaar",
            'description' => "This bazaar will happen soon.",
            'start_date' => $now->addDays(5),
            'end_date' => $now->addDays(10),
            'start_requesting_date' => $now->subDay(1),
            'end_requesting_date' => $now->addDays(4),
            'user_id' => $allUsers->random()->id,
            'address_id' => Address::inRandomOrder()->first()->id,
            'location_type' => 'onsite',
            'category_id' => 3,
        ]);
        $this->attachImage($upcomingBazaar, "bazaars/{$bazaarImages[2]}");
        $upcomingBazaar->products()->attach($allProducts->random(7)->pluck('id')->toArray());
        $this->addCommentsAndUpdateRating($upcomingBazaar, $allUsers);
    }

    private function attachImage($model, $relativePath)
    {
        $baseDir = pathinfo($relativePath, PATHINFO_DIRNAME);
        $filename = pathinfo($relativePath, PATHINFO_FILENAME);

        $possibleExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $sourcePath = null;
        $targetPath = null;

        foreach ($possibleExtensions as $ext) {
            $try = database_path("seeders/images/{$baseDir}/{$filename}.{$ext}");
            if (file_exists($try)) {
                $sourcePath = $try;
                $targetPath = "seed/{$baseDir}/{$filename}.{$ext}";
                break;
            }
        }

        // fallback to a default image
        if (!$sourcePath) {
            $sourcePath = database_path("seeders/images/default.jpg");
            $targetPath = "seed/default.jpg";
        }

        Storage::disk('public')->put($targetPath, file_get_contents($sourcePath));

        Image::create([
            'path' => $targetPath,
            'imageable_id' => $model->id,
            'imageable_type' => get_class($model),
        ]);
    }

    private function addCommentsAndUpdateRating($model, $users)
    {
        $ratings = [];
        $count = rand(2, 5); // number of comments per model

        for ($i = 0; $i < $count; $i++) {
            $user = $users->random();
            $rating = rand(1, 5);
            $ratings[] = $rating;

            Comment::create([
                'user_id' => $user->id,
                'body' => "This is a comment {$i} for " . class_basename($model) . " #{$model->id}",
                'rating' => $rating,
                'sentiment' => $this->sentimentFromRating($rating),
                'commentable_id' => $model->id,
                'commentable_type' => get_class($model),
            ]);
        }

        if (count($ratings)) {
            $model->rating_count = count($ratings);
            $model->rating = round(array_sum($ratings) / count($ratings));
            $model->save();
        }
    }

    private function sentimentFromRating($rating)
    {
        return match (true) {
            $rating >= 4 => 'positive',
            $rating == 3 => 'neutral',
            default => 'negative',
        };
    }
}
