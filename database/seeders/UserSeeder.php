<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    User::factory()->create([
      'name' => 'BikeTrack Admin',
      'email' => 'admin@example.com',
      'password' => bcrypt('password'),
      'role_id' => 1,
      'approval_status' => 'active',
    ]);

    User::factory()->create([
      'name' => 'BikeTrack Staff',
      'email' => 'staff@example.com',
      'password' => bcrypt('password'),
      'role_id' => 2,
      'approval_status' => 'active',
    ]);

    User::factory()->create([
      'name' => 'Test Admin',
      'email' => 'test@test.com',
      'password' => bcrypt('qwerty123'),
      'role_id' => 1,
      'approval_status' => 'active',
    ]);

    User::factory()->create([
      'name' => 'Test Staff',
      'email' => 'staff@test.com',
      'password' => bcrypt('qwerty123'),
      'role_id' => 2,
      'approval_status' => 'active',
    ]);
  }
}
