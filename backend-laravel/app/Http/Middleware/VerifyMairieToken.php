<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMairieToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.mairie.token');
        $provided = (string) $request->header('X-Mairie-Token', '');

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            return response()->json([
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
