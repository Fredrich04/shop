<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro',
                'slug' => 'iphone-15-pro',
                'description' => 'Le dernier iPhone avec puce A17 Pro et appareil photo révolutionnaire',
                'price' => 1299.00,
                'image_url' => 'https://images.unsplash.com/photo-1640948612546-3b9e29c23e98?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxtb2Rlcm4lMjBzbWFydHBob25lJTIwdGVjaG5vbG9neXxlbnwxfHx8fDE3NTg3NzkyMDR8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 15,
                'category_slug' => 'smartphone'
            ],
            [
                'name' => 'MacBook Air M3',
                'slug' => 'macbook-air-m3',
                'description' => 'Ordinateur portable ultra-fin avec puce M3 pour des performances exceptionnelles',
                'price' => 1499.00,
                'image_url' => 'https://images.unsplash.com/photo-1643290369779-c6bec760cf18?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxsYXB0b3AlMjBjb21wdXRlciUyMGVsZWN0cm9uaWNzfGVufDF8fHx8MTc1ODgxMDYxMnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 8,
                'category_slug' => 'ordinateur'
            ],
            [
                'name' => 'AirPods Pro 2',
                'slug' => 'airpods-pro-2',
                'description' => 'Écouteurs sans fil avec réduction de bruit active de nouvelle génération',
                'price' => 299.00,
                'image_url' => 'https://images.unsplash.com/photo-1632200004922-bc18602c79fc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx3aXJlbGVzcyUyMGhlYWRwaG9uZXMlMjBhdWRpb3xlbnwxfHx8fDE3NTg4NTkwODZ8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 25,
                'category_slug' => 'audio'
            ],
            [
                'name' => 'Apple Watch Series 9',
                'slug' => 'apple-watch-series-9',
                'description' => 'Montre connectée avec GPS, monitoring santé et écran Always-On',
                'price' => 449.00,
                'image_url' => 'https://images.unsplash.com/photo-1665860455418-017fa50d29bc?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxzbWFydHdhdGNoJTIwZml0bmVzcyUyMHRyYWNrZXJ8ZW58MXx8fHwxNzU4ODM1NTgzfDA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 12,
                'category_slug' => 'wearable'
            ],
            [
                'name' => 'iPad Pro 12.9"',
                'slug' => 'ipad-pro-12-9',
                'description' => 'Tablette professionnelle avec puce M2 et écran Liquid Retina XDR',
                'price' => 1199.00,
                'image_url' => 'https://images.unsplash.com/photo-1681178519367-32c366c98867?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHx0YWJsZXQlMjBkZXZpY2UlMjBkaWdpdGFsfGVufDF8fHx8MTc1ODg1NDY2Mnww&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 6,
                'category_slug' => 'tablette'
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'slug' => 'canon-eos-r6-mark-ii',
                'description' => 'Appareil photo hybride haute performance pour les professionnels',
                'price' => 2399.00,
                'image_url' => 'https://images.unsplash.com/photo-1729857037662-221cc636782a?crop=entropy&cs=tinysrgb&fit=max&fm=jpg&ixid=M3w3Nzg4Nzd8MHwxfHNlYXJjaHwxfHxjYW1lcmElMjBwaG90b2dyYXBoeSUyMGVxdWlwbWVudHxlbnwxfHx8fDE3NTg3NzQ0MzB8MA&ixlib=rb-4.1.0&q=80&w=1080&utm_source=figma&utm_medium=referral',
                'stock' => 30,
                'category_slug' => 'photo'
            ],
            [
                'name' => 'Samsung S25 edge',
                'slug' => 'samsung-s25-edge',
                'description' => 'Smartphone ultra performant',
                'price' => 1099.00,
                'image_url' => 'https://cdn.lesnumeriques.com/optim/product/76/76229/0b36dacb-galaxy-s25-edge__1200_1200.webp',
                'stock' => 40,
                'category_slug' => 'Smartphone'
            ],
            [
                'name' => 'Samsung S25 ultra',
                'slug' => 'samsung-s25-ultra',
                'description' => 'Dernier smartphone de la gamme S de Samsung',
                'price' => 813.39,
                'image_url' => 'https://images.samsung.com/is/image/samsung/assets/sg/smartphones/galaxy-s25-ultra/buy/kv_global_PC_SG.jpg?imbypass=true',
                'stock' => 24,
                'category_slug' => 'Smartphone'
            ],
            [
                'name' => 'XP-PEN Deco Pro LW',
                'slug' => 'xp-pen-deco-pro-lw',
                'description' => 'tablette graphique',
                'price' => 179.99,
                'image_url' => 'https://resource.xp-pen.com/Uploads/goods/20230508/d031010f32587dc5c2374fd0cd80abf9.webp',
                'stock' => 30,
                'category_slug' => 'tablette'
            ],
            [
                'name' => 'Google Pixel 9 Pro XL',
                'slug' => 'google-pixel-9-pro-xl',
                'description' => 'Les téléphones Pixel 9 Pro, avec Gemini, votre assistant IA intégré',
                'price' => 999.99,
                'image_url' => 'https://storage.googleapis.com/gweb-uniblog-publish-prod/images/image_P9_2024Q2_Peony_LT_T-Shot_.width-1000.format-webp_SVFKGQ9.webp',
                'stock' => 150,
                'category_slug' => 'smartphone'
            ]
        ];

        foreach ($products as $productData) {
            $category = Category::where('slug', $productData['category_slug'])->first();

            if ($category) {
                Product::create([
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'image_url' => $productData['image_url'],
                    'stock' => $productData['stock'],
                    'category_id' => $category->id,
                    'is_active' => true
                ]);
            }
        }
    }
}
