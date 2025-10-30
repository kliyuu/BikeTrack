<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('authenticated users can visit the dashboard', function () {
    $this->actingAs($user = User::factory()->create());
    $user->acceptTos(); // Accept TOS for the test user

    $response = $this->get('/dashboard');
    $response->assertRedirect(); // Should redirect to role-based dashboard
});
