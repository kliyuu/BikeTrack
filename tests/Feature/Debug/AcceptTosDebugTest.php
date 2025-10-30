<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Livewire;

test('debug: accept tos component works', function () {
    $user = User::factory()->create(['tos_accepted_at' => null]);

    $this->actingAs($user);

    // Test the component directly
    $component = Livewire::test(\App\Livewire\Auth\AcceptTos::class)
        ->assertSet('accept_tos', false);

    // Set the checkbox to true
    $component->set('accept_tos', true);

    // Call the method directly
    $component->call('acceptTerms');

    // Check if the user's TOS was updated
    $user->refresh();
    expect($user->hasAcceptedTos())->toBeTrue();
    expect($user->tos_accepted_at)->not->toBeNull();

    // Check if redirect happened
    $component->assertRedirect(route('dashboard'));
});

test('debug: accept tos validation works', function () {
    $user = User::factory()->create(['tos_accepted_at' => null]);

    $this->actingAs($user);

    // Test validation failure with false value
    Livewire::test(\App\Livewire\Auth\AcceptTos::class)
        ->set('accept_tos', false)
        ->call('acceptTerms')
        ->assertHasErrors(['accept_tos']);

    // User should not have accepted TOS
    $user->refresh();
    expect($user->hasAcceptedTos())->toBeFalse();
});
