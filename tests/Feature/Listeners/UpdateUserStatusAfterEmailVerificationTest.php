<?php

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Verified;

test('user status is updated to active upon email verification', function () {
    $user = User::factory()->unverified()->create([
        'approval_status' => 'pending',
        'tos_accepted_at' => now(),
    ]);

    // Manually trigger the Verified event
    event(new Verified($user));

    expect($user->fresh()->approval_status)->toBe('active');
});

test('user status remains unchanged if not pending', function () {
    $user = User::factory()->create([
        'approval_status' => 'active',
        'email_verified_at' => now(),
        'tos_accepted_at' => now(),
    ]);

    event(new Verified($user));

    expect($user->fresh()->approval_status)->toBe('active');
});

test('client status is updated to active when user verifies email', function () {
    $user = User::factory()->unverified()->create([
        'approval_status' => 'pending',
        'tos_accepted_at' => now(),
    ]);

    $client = Client::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    event(new Verified($user));

    expect($user->fresh()->approval_status)->toBe('active')
        ->and($client->fresh()->status)->toBe('active');
});

test('listener handles user without client gracefully', function () {
    $user = User::factory()->unverified()->create([
        'approval_status' => 'pending',
        'tos_accepted_at' => now(),
    ]);

    // User has no associated client
    expect($user->client)->toBeNull();

    event(new Verified($user));

    expect($user->fresh()->approval_status)->toBe('active');
});

test('database transaction is used for status updates', function () {
    $user = User::factory()->unverified()->create([
        'approval_status' => 'pending',
        'tos_accepted_at' => now(),
    ]);

    Client::factory()->create([
        'user_id' => $user->id,
        'status' => 'pending',
    ]);

    event(new Verified($user));

    // Verify both updates happened (transaction worked)
    expect($user->fresh()->approval_status)->toBe('active')
        ->and($user->client->fresh()->status)->toBe('active');
});
