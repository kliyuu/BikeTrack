<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $roles = [
      ['name' => 'admin', 'description' => 'Administrator: Full system access'],
      ['name' => 'staff', 'description' => 'Staff User: Can manage orders, stock, and clients'],
      ['name' => 'client', 'description' => 'Client User: B2B client with purchasing rights'],
    ];

    foreach ($roles as $role) {
      Role::create($role);
    }
  }
}
