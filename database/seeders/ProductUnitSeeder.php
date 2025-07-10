<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductUnit;

class ProductUnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            'Pcs',
            'Box',
            'Kg',
            'Litre',
            'Meter',
            'Pack',
            'Set',
            'Bottle',
            'Sheet',
            'Others',
        ];
        foreach ($units as $unit) {
            ProductUnit::firstOrCreate(['name' => $unit]);
        }
    }
} 