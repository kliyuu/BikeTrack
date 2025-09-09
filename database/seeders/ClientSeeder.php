<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ClientSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $client1 = User::create([
      'name' => 'John Doe',
      'email' => 'client@test.com',
      'password' => bcrypt('qwerty123'),
      'role_id' => 3,
      'approval_status' => 'active'
    ]);

    Client::create([
      'user_id' => $client1->id,
      'code' => 'CLT001',
      'company_name' => 'MotoParts Trading',
      'contact_name' => 'Juan Dela Cruz',
      'contact_email' => 'juan@motoparts.com',
      'contact_phone' => '09171234567',
      'billing_address' => '123 Motorcycle Ave, Quezon City, Metro Manila',
      'shipping_address' => '123 Motorcycle Ave, Quezon City, Metro Manila',
      'tax_number' => 'TIN987654321',
      'payment_method' => 'Bank Transfer',
      'status' => 'active'
    ]);

    $client2 = User::create([
      'name' => 'Jane Smith',
      'email' => 'client@example.com',
      'password' => bcrypt('password'),
      'role_id' => 3,
      'approval_status' => 'pending'
    ]);

    Client::create([
      'user_id' => $client2->id,
      'code' => 'CLT002',
      'company_name' => 'BikerZone Supply',
      'contact_name' => 'Maria Santos',
      'contact_email' => 'maria@bikerzone.com',
      'contact_phone' => '09179876543',
      'billing_address' => '456 Rider St, Makati City, Metro Manila',
      'shipping_address' => '456 Rider St, Makati City, Metro Manila',
      'tax_number' => 'TIN123456789',
      'payment_method' => 'GCash',
      'status' => 'pending'
    ]);
  }
}
