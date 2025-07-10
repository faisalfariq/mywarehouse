<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductUnit;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ProductCategory::pluck('id')->toArray();
        $units = ProductUnit::pluck('id')->toArray();

        $products = [
            [
                'product_name' => 'Laptop Dell Inspiron',
                'product_code' => 'LAP001',
                'category_id' => fake()->randomElement($categories),
                'unit_id' => fake()->randomElement($units),
                'description' => 'Dell Inspiron 15-inch laptop with Intel i5 processor',
            ],
            [
                'product_name' => 'Wireless Mouse',
                'product_code' => 'ACC001',
                'category_id' => fake()->randomElement($categories),
                'unit_id' => fake()->randomElement($units),
                'description' => 'Logitech wireless mouse with USB receiver',
            ],
            [
                'product_name' => 'Office Chair',
                'product_code' => 'FUR001',
                'category_id' => fake()->randomElement($categories),
                'unit_id' => fake()->randomElement($units),
                'description' => 'Ergonomic office chair with adjustable height',
            ],
            [
                'product_name' => 'Printer Paper A4',
                'product_code' => 'PAP001',
                'category_id' => fake()->randomElement($categories),
                'unit_id' => fake()->randomElement($units),
                'description' => 'A4 printer paper, 80gsm, 500 sheets per ream',
            ],
            [
                'product_name' => 'Coffee Maker',
                'product_code' => 'APP001',
                'category_id' => fake()->randomElement($categories),
                'unit_id' => fake()->randomElement($units),
                'description' => 'Automatic coffee maker with timer',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
