<?php

use App\Livewire\Auth\Register;
use App\Models\Client;
use App\Models\User;
use Livewire\Livewire;

test('user can register with company details', function () {
    // Seed roles first
    $this->seed(\Database\Seeders\RoleSeeder::class);

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'company_name' => 'Test Company Ltd',
        'tax_number' => 'TAX123456789',
        'contact_name' => 'John Doe',
        'contact_email' => 'john@example.com',
        'contact_phone' => '+1234567890',
        'billing_address' => '123 Test Street, Test City, TC 12345',
    ];

    Livewire::test(Register::class)
        ->set('name', $userData['name'])
        ->set('email', $userData['email'])
        ->set('password', $userData['password'])
        ->set('password_confirmation', $userData['password_confirmation'])
        ->set('company_name', $userData['company_name'])
        ->set('tax_number', $userData['tax_number'])
        ->set('contact_name', $userData['contact_name'])
        ->set('contact_email', $userData['contact_email'])
        ->set('contact_phone', $userData['contact_phone'])
        ->set('billing_address', $userData['billing_address'])
        ->set('accept_tos', true)
        ->call('register')
        ->assertRedirect(route('dashboard'));

    // Assert user was created
    $user = User::where('email', $userData['email'])->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe($userData['name'])
        ->and($user->role_id)->toBe(3)
        ->and($user->approval_status)->toBe('pending')
        ->and($user->hasAcceptedTos())->toBeTrue();

    // Assert client was created with unique code
    $client = $user->client;
    expect($client)->not->toBeNull()
        ->and($client->company_name)->toBe($userData['company_name'])
        ->and($client->tax_number)->toBe($userData['tax_number'])
        ->and($client->contact_name)->toBe($userData['contact_name'])
        ->and($client->contact_email)->toBe($userData['contact_email'])
        ->and($client->contact_phone)->toBe($userData['contact_phone'])
        ->and($client->billing_address)->toBe($userData['billing_address'])
        ->and($client->shipping_address)->toBe($userData['billing_address']) // Should default to billing address
        ->and($client->code)->toMatch('/^CLT-[A-F0-9]{6,12}$/'); // Should match the pattern
});

test('generated client codes are unique', function () {
    // Seed roles first
    $this->seed(\Database\Seeders\RoleSeeder::class);

    // Create multiple users to test uniqueness
    $users = [];
    for ($i = 0; $i < 5; $i++) {
        $userData = [
            'name' => "User $i",
            'email' => "user$i@example.com",
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => "Company $i",
            'tax_number' => "TAX$i",
            'contact_name' => "Contact $i",
            'contact_email' => "contact$i@example.com",
            'contact_phone' => "+123456789$i",
            'billing_address' => "Address $i",
        ];

        Livewire::test(Register::class)
            ->set('name', $userData['name'])
            ->set('email', $userData['email'])
            ->set('password', $userData['password'])
            ->set('password_confirmation', $userData['password_confirmation'])
            ->set('company_name', $userData['company_name'])
            ->set('tax_number', $userData['tax_number'])
            ->set('contact_name', $userData['contact_name'])
            ->set('contact_email', $userData['contact_email'])
            ->set('contact_phone', $userData['contact_phone'])
            ->set('billing_address', $userData['billing_address'])
            ->set('accept_tos', true)
            ->call('register');

        $users[] = User::where('email', $userData['email'])->first();
    }

    // Check that all client codes are unique
    $codes = collect($users)->map(fn ($user) => $user->client->code);
    expect($codes->unique()->count())->toBe(5);
});

test('registration validates required fields', function () {
    Livewire::test(Register::class)
        ->call('register')
        ->assertHasErrors(['name', 'email', 'password', 'accept_tos']);
});

test('registration validates unique email', function () {
    // Seed roles first
    $this->seed(\Database\Seeders\RoleSeeder::class);

    User::factory()->create(['email' => 'existing@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'existing@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('company_name', 'Test Company')
        ->set('tax_number', 'TAX123')
        ->set('contact_name', 'John Doe')
        ->set('contact_email', 'john@example.com')
        ->set('contact_phone', '+1234567890')
        ->set('billing_address', '123 Test Street')
        ->set('accept_tos', true)
        ->call('register')
        ->assertHasErrors('email');
});

test('registration requires TOS acceptance', function () {
    // Seed roles first
    $this->seed(\Database\Seeders\RoleSeeder::class);

    Livewire::test(Register::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->set('company_name', 'Test Company')
        ->set('tax_number', 'TAX123')
        ->set('contact_name', 'John Doe')
        ->set('contact_email', 'john@example.com')
        ->set('contact_phone', '+1234567890')
        ->set('billing_address', '123 Test Street')
        ->set('accept_tos', false)
        ->call('register')
        ->assertHasErrors('accept_tos');
});
