<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // if ($user->approval_status !== 'active') {
        //   auth()->logout();
        //   return redirect()
        //     ->route('login')
        //     ->withErrors(['email' => 'Your account is not active. Please contact BikeTrack support.']);
        // }

        if ($user->approval_status === 'pending') {
            auth()->logout();

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your account is pending approval. Please contact BikeTrack support.']);
        }

        if (! $user->hasAnyRole($roles)) {
            abort(403, 'You do not have the required permissions to access this resource.');
        }

        return $next($request);
    }
}
