<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'location_name' => 'Warehouse A',
                'location_code' => 'WH-A',
                'address' => 'Jl. Industri No. 123, Jakarta',
                'description' => 'Main warehouse for electronics and accessories',
            ],
            [
                'location_name' => 'Warehouse B',
                'location_code' => 'WH-B',
                'address' => 'Jl. Komersial No. 456, Bandung',
                'description' => 'Secondary warehouse for furniture and appliances',
            ],
            [
                'location_name' => 'Office Storage',
                'location_code' => 'OFF-STOR',
                'address' => 'Jl. Kantor No. 789, Jakarta',
                'description' => 'Storage area for office supplies',
            ],
            [
                'location_name' => 'Distribution Center',
                'location_code' => 'DC-01',
                'address' => 'Jl. Distribusi No. 321, Surabaya',
                'description' => 'Central distribution center for all products',
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
