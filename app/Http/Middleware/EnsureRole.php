<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('role:admin') or ->middleware('role:admin,chofer')
     */
    public function handle(Request $request, Closure $next, string $roles = null): Response
    {
        $user = $request->user();

        // Abort if the user is not authenticated.  While the `auth` middleware
        // normally runs before this one, making this check explicit makes the
        // control flow more obvious and prevents errors when the middleware is
        // mistakenly used without auth.
        if (! $user) {
            abort(403);
        }

        // If no roles were specified on the middleware declaration, simply
        // continue processing the request.  This allows `->middleware('role')`
        // without parameters to act as a passthrough and is backwards‑compatible
        // with earlier implementations.
        if (! $roles) {
            return $next($request);
        }

        // Convert the comma‑separated list of roles to an array and trim
        // whitespace on each element.  For example, 'admin, gestor' becomes
        // ['admin', 'gestor'].
        $roleArray = array_map(function ($r) {
            return trim($r);
        }, explode(',', $roles));

        // Use the user model’s hasRole helper to check membership.  This helper
        // normalizes case and accepts both strings and arrays, so it properly
        // handles values like 'Gestor' or 'gestor'.
        if ($user->hasRole($roleArray)) {
            return $next($request);
        }

        // If the user’s role is not in the allowed list, deny access.
        abort(403);
    }
}
