<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

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
            'approval_status' => 'active',
        ]);

        Client::create([
            'user_id' => $client1->id,
            'code' => 'CLT001',
            'company_name' => 'MotoParts Trading',
            'contact_name' => 'John Doe',
            'contact_email' => 'john@motoparts.com',
            'contact_phone' => '09171234567',
            'billing_address' => '123 Motorcycle Ave, Quezon City, Metro Manila',
            'shipping_address' => '123 Motorcycle Ave, Quezon City, Metro Manila',
            'tax_number' => 'TIN987654321',
            'payment_method' => 'Bank Transfer',
            'status' => $client1->approval_status,
        ]);

        $client2 = User::create([
            'name' => 'Jane Smith',
            'email' => 'client2@test.com',
            'password' => bcrypt('qwerty123'),
            'role_id' => 3,
            'approval_status' => 'active',
        ]);

        Client::create([
            'user_id' => $client2->id,
            'code' => 'CLT002',
            'company_name' => 'BikerZone Supply',
            'contact_name' => 'Jane Smith',
            'contact_email' => 'jane@bikerzone.com',
            'contact_phone' => '09179876543',
            'billing_address' => '456 Rider St, Makati City, Metro Manila',
            'shipping_address' => '456 Rider St, Makati City, Metro Manila',
            'tax_number' => 'TIN123456789',
            'payment_method' => 'GCash',
            'status' => $client2->approval_status,
        ]);

        $client3 = User::create([
            'name' => 'Alice Johnson',
            'email' => 'client3@test.com',
            'password' => bcrypt('qwerty123'),
            'role_id' => 3,
            'approval_status' => 'pending',
        ]);

        Client::create([
            'user_id' => $client3->id,
            'code' => 'CLT003',
            'company_name' => 'Bicycle Emporium',
            'contact_name' => 'Alice Johnson',
            'contact_email' => 'alice@bicycleemporium.com',
            'contact_phone' => '09123456789',
            'billing_address' => '789 Bicycle Rd, Mandaluyong City, Metro Manila',
            'shipping_address' => '789 Bicycle Rd, Mandaluyong City, Metro Manila',
            'tax_number' => 'TIN876543210',
            'payment_method' => 'Cash on Delivery',
            'status' => $client3->approval_status,
        ]);
    }
}
