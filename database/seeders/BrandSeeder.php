<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Brand::firstOrCreate([
            'name' => 'Generic',
            'slug' => 'generic',
            'description' => 'Unbranded or generic parts',
            'is_generic' => true,
        ]);

        Brand::firstOrCreate([
            'name' => 'Yamaha',
            'slug' => 'yamaha',
            'description' => 'Yamaha Genuine Parts',
            'is_generic' => false,
        ]);

        Brand::firstOrCreate([
            'name' => 'Honda',
            'slug' => 'honda',
            'description' => 'Honda Genuine Parts',
            'is_generic' => false,
        ]);

        Brand::firstOrCreate([
            'name' => 'Kawasaki',
            'slug' => 'kawasaki',
            'description' => 'Kawasaki Genuine Parts',
            'is_generic' => false,
        ]);
    }
}
