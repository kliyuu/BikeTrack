<?php

declare(strict_types=1);

use App\Livewire\Notifications\AdminIndex;
use App\Models\Notification;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

test('admin can view notifications index page', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    $response = $this->get(route('admin.notifications'));

    $response->assertStatus(200);
    $response->assertSeeLivewire(AdminIndex::class);
});

test('admin can see their notifications', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'title' => 'Test Admin Notification',
        'message' => 'This is a test notification for admin',
        'is_read' => false,
    ]);

    Livewire::test(AdminIndex::class)
        ->assertSee('Test Admin Notification')
        ->assertSee('This is a test notification for admin');
});

test('admin can filter notifications by unread', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'title' => 'Unread Notification',
        'is_read' => false,
    ]);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'title' => 'Read Notification',
        'is_read' => true,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('setFilter', 'unread')
        ->assertSee('Unread Notification')
        ->assertDontSee('Read Notification');
});

test('admin can filter notifications by read', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'title' => 'Unread Notification',
        'is_read' => false,
    ]);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'title' => 'Read Notification',
        'is_read' => true,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('setFilter', 'read')
        ->assertDontSee('Unread Notification')
        ->assertSee('Read Notification');
});

test('admin can mark notification as read', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    $notification = Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'is_read' => false,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('markAsRead', $notification->id)
        ->assertDispatched('showToast');

    expect($notification->fresh()->is_read)->toBeTrue();
});

test('admin can mark all notifications as read', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    Notification::factory()->count(3)->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'is_read' => false,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('markAllAsRead')
        ->assertDispatched('showToast');

    expect(Notification::where('client_id', null)->where('is_read', false)->count())->toBe(0);
});

test('admin can delete a notification', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    $notification = Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('deleteNotification', $notification->id)
        ->assertDispatched('showToast');

    expect(Notification::find($notification->id))->toBeNull();
});

test('admin can delete all read notifications', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    Notification::factory()->count(2)->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'is_read' => true,
    ]);

    Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => null,
        'is_read' => false,
    ]);

    Livewire::test(AdminIndex::class)
        ->call('deleteAllRead')
        ->assertDispatched('showToast');

    expect(Notification::where('client_id', null)->where('is_read', true)->count())->toBe(0);
    expect(Notification::where('client_id', null)->where('is_read', false)->count())->toBe(1);
});

test('admin cannot see client notifications', function () {
    $admin = User::factory()->create(['role_id' => 1]);
    actingAs($admin);

    $client = \App\Models\Client::factory()->create();

    $clientNotification = Notification::factory()->create([
        'user_id' => $admin->id,
        'client_id' => $client->id,
        'title' => 'Client Notification',
    ]);

    Livewire::test(AdminIndex::class)
        ->assertDontSee('Client Notification');

    expect(
        Notification::where('client_id', null)->where('id', $clientNotification->id)->exists()
    )->toBeFalse();
});
