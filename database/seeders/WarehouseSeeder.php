<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    Warehouse::firstOrCreate([
      'name' => 'Main Warehouse',
      'location' => 'Sariaya Quezon, PH',
      'contact_number' => '1234567890',
      'contact_person' => 'John Doe',
      'contact_email' => 'biketrack@example.com'
    ]);
  }
}
