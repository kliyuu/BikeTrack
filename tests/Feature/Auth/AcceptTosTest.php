<?php

use App\Livewire\Auth\AcceptTos;
use App\Models\User;
use Livewire\Livewire;

test('accept tos screen can be rendered', function () {
    $user = User::factory()->create(['tos_accepted_at' => null]);
    $this->actingAs($user);

    $response = $this->get('/accept-tos');

    $response->assertStatus(200);
});

test('users can accept terms of service via form', function () {
    $user = User::factory()->create(['tos_accepted_at' => null]);
    $this->actingAs($user);

    Livewire::test(AcceptTos::class)
        ->set('accept_tos', true)
        ->call('acceptTerms')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->hasAcceptedTos())->toBeTrue();
});

test('users cannot continue without accepting terms of service via form', function () {
    $user = User::factory()->create(['tos_accepted_at' => null]);
    $this->actingAs($user);

    Livewire::test(AcceptTos::class)
        ->set('accept_tos', false)
        ->call('acceptTerms')
        ->assertHasErrors(['accept_tos']);

    $user->refresh();
    expect($user->hasAcceptedTos())->toBeFalse();
});

test('users who have already accepted TOS are redirected to dashboard when visiting TOS page', function () {
    $user = User::factory()->create(['tos_accepted_at' => now()]);
    $this->actingAs($user);

    $response = Livewire::test(AcceptTos::class);

    $response->assertRedirect(route('dashboard'));
});
