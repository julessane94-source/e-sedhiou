<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    /**
     * Usage : 'role:admin' ou 'role:admin,agent'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            return response()->json([
                'message' => 'Accès refusé. Rôle requis : ' . implode(' ou ', $roles) . '.',
            ], 403);
        }

        return $next($request);
    }
}
