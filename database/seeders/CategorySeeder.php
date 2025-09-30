<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Désactiver temporairement les contraintes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Category::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'Smartphone', 'slug' => 'smartphone', 'description' => 'Téléphones intelligents et accessoires'],
            ['name' => 'Ordinateur', 'slug' => 'ordinateur', 'description' => 'Ordinateurs portables et de bureau'],
            ['name' => 'Audio', 'slug' => 'audio', 'description' => 'Écouteurs, casques et équipements audio'],
            ['name' => 'Wearable', 'slug' => 'wearable', 'description' => 'Montres connectées et objets portables'],
            ['name' => 'Tablette', 'slug' => 'tablette', 'description' => 'Tablettes et accessoires'],
            ['name' => 'Photo', 'slug' => 'photo', 'description' => 'Appareils photo et équipements photographiques'],
            ['name' => 'Clé', 'slug' => 'key', 'description' => 'Appareil de stockage'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
