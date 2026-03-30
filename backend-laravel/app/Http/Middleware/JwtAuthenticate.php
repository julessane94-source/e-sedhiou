<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthenticate
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (! $user) {
                return response()->json(['message' => 'Utilisateur introuvable.'], 401);
            }
            if (! $user->is_active) {
                return response()->json(['message' => 'Compte désactivé.'], 403);
            }
        } catch (TokenExpiredException) {
            return response()->json(['message' => 'Session expirée. Reconnectez-vous.'], 401);
        } catch (TokenInvalidException) {
            return response()->json(['message' => 'Token invalide.'], 401);
        } catch (JWTException) {
            return response()->json(['message' => 'Token absent ou malformé.'], 401);
        }

        return $next($request);
    }
}
