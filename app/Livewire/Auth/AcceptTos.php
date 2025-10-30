<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Accept Terms of Service')]
class AcceptTos extends Component
{
    public bool $accept_tos = false;

    public function mount()
    {
        // If user has already accepted TOS, redirect to dashboard
        if (Auth::user()->hasAcceptedTos()) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function acceptTerms()
    {
        $this->validate([
            'accept_tos' => ['required', 'accepted'],
        ], [
            'accept_tos.required' => 'You must accept the Terms of Service to continue.',
            'accept_tos.accepted' => 'You must accept the Terms of Service to continue.',
        ]);

        try {
            $user = Auth::user();
            $user->acceptTos();

            session()->flash('status', 'Terms of Service accepted successfully!');

            $this->redirect(route('dashboard', absolute: false), navigate: true);
        } catch (\Exception $e) {
            Log::error('AcceptTerms failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'accept_tos' => $this->accept_tos,
            ]);

            session()->flash('error', 'An error occurred while accepting terms. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.auth.accept-tos');
    }
}
