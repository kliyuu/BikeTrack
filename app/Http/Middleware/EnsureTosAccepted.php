<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTosAccepted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! Auth::user()->hasAcceptedTos()) {
            // Skip TOS check for TOS acceptance route to avoid infinite redirect
            // if ($request->routeIs('tos.accept')) {
            //     return $next($request);
            // }

            // // Skip TOS check for logout route
            // if ($request->routeIs('logout')) {
            //     return $next($request);
            // }

            // // Skip TOS check for Livewire requests - they need to work on the TOS page
            // if ($request->is('livewire/*')) {
            //     return $next($request);
            // }

            $excludedRoutes = ['tos.accept', 'logout'];
            if ($request->routeIs($excludedRoutes) || $request->is('livewire/*')) {
                return $next($request);
            }

            // Redirect to TOS acceptance page
            return redirect()->route('tos.accept');
        }

        return $next($request);
    }
}
