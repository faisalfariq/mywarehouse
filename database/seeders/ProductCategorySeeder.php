<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electronics',
            'Food',
            'Beverages',
            'Stationery',
            'Clothing',
            'Accessories',
            'Tools',
            'Others',
        ];
        foreach ($categories as $cat) {
            ProductCategory::firstOrCreate(['name' => $cat]);
        }
    }
} 