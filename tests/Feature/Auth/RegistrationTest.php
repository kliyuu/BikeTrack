<?php

use App\Livewire\Auth\Register;
use Livewire\Livewire;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Seed roles first
    $this->seed(\Database\Seeders\RoleSeeder::class);

    $response = Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('company_name', 'Test Company')
        ->set('tax_number', 'TAX123')
        ->set('contact_name', 'Test User')
        ->set('contact_email', 'test@example.com')
        ->set('contact_phone', '+1234567890')
        ->set('billing_address', '123 Test Street')
        ->set('accept_tos', true)
        ->call('register');

    $response
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
