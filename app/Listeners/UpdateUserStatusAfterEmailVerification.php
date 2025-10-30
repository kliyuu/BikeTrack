<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\DB;

class UpdateUserStatusAfterEmailVerification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        DB::transaction(function () use ($event) {
            $user = $event->user;

            // Update user status to 'active' upon email verification
            if ($user->approval_status === 'pending') {
                $user->update(['approval_status' => 'active']);
            }

            // If the user is associated with a client, update the client's status as well
            if ($user->client) {
                $user->client->update(['status' => 'active']);
            }
        });
    }
}
