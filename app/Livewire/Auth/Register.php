<?php

namespace App\Livewire\Auth;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.auth')]
#[Title('Register')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    // Company related fields
    public string $company_name = '';

    public string $tax_number = '';

    public string $contact_name = '';

    public string $contact_email = '';

    public string $contact_phone = '';

    public string $billing_address = '';

    public bool $accept_tos = false;

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->letters()
                    ->symbols(),
            ],
            'accept_tos' => ['required', 'accepted'],
        ]);

        $companyData = $this->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'tax_number' => ['required', 'string', 'max:255'],
            // 'contact_name' => ['required', 'string', 'max:255'],
            // 'contact_email' => ['required', 'string', 'email', 'max:255'],
            'contact_phone' => ['required', 'string', 'max:255'],
            'billing_address' => ['required', 'string', 'max:255'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role_id'] = 3; // Client role
        $validated['approval_status'] = 'pending'; // default approval status
        // $validated['tos_accepted_at'] = now(); // Set TOS acceptance timestamp

        // Remove accept_tos from the array as it's not a database field
        unset($validated['accept_tos']);

        event(new Registered(($user = User::create($validated))));

        // Generate unique client code with retry limit
        $companyData['code'] = $this->generateUniqueClientCode();

        // Add shipping address (default to billing address if not provided)
        $companyData['shipping_address'] = $companyData['billing_address'];

        $user->client()->create($companyData);

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    private function generateUniqueClientCode(): string
    {
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $code = 'CLT-'.strtoupper(bin2hex(random_bytes(3)));
            $attempts++;

            // Check if code already exists
            $exists = Client::where('code', $code)->exists();

            if (! $exists) {
                return $code;
            }

            if ($attempts >= $maxAttempts) {
                // If we've tried 10 times, use a longer random string
                $code = 'CLT-'.strtoupper(bin2hex(random_bytes(6)));

                // Final check - if this still exists, it's extremely unlikely but we'll return it anyway
                // as the database unique constraint will handle the final validation
                return $code;
            }
        } while ($attempts < $maxAttempts);

        // This should never be reached, but just in case
        return 'CLT-'.strtoupper(bin2hex(random_bytes(6)));
    }
}
