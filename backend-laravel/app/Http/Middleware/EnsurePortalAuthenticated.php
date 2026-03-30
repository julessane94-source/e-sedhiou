<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $session = $request->session()->get('mairie_portal_auth');
        $userId = is_array($session) ? (int) ($session['user_id'] ?? 0) : 0;
        $redirectParams = ['redirect_to' => $request->fullUrl()];

        if ($userId < 1) {
            return redirect()->route('portal.auth', $redirectParams);
        }

        $user = User::find($userId);
        if (! $user || ! $user->is_active) {
            $request->session()->forget('mairie_portal_auth');
            return redirect()->route('portal.auth', $redirectParams)->withErrors([
                'email' => 'Votre session a expiré. Reconnectez-vous.',
            ]);
        }

        return $next($request);
    }
}
