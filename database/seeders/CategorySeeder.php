<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Category::firstOrCreate([
      'name' => 'Engine Parts',
      'slug' => 'engine-parts',
      'description' => 'All types of motorcycle engine components'
    ]);

    Category::firstOrCreate([
      'name' => 'Tires',
      'slug' => 'tires',
      'description' => 'Motorcycle tires and related accessories'
    ]);

    Category::firstOrCreate([
      'name' => 'Breaks',
      'slug' => 'breaks',
      'description' => 'High-performance motorcycle brakes and components'
    ]);

    Category::firstOrCreate([
      'name' => 'Suspension',
      'slug' => 'suspension',
      'description' => 'Suspension systems and parts for motorcycles'
    ]);
  }
}
