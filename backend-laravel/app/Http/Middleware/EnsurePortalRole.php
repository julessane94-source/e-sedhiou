<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
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
            return redirect()->route('portal.auth', $redirectParams);
        }

        $normalizedRole = User::normalizeRoleValue((string) $user->role);
        $normalizedAllowed = array_map(
            static fn (string $role): string => User::normalizeRoleValue($role),
            $roles
        );

        if (! in_array($normalizedRole, $normalizedAllowed, true)) {
            return redirect()->route('portal.entry')->with([
                'status' => 'Accès réorienté vers votre espace autorisé.',
                'status_type' => 'warning',
            ]);
        }

        return $next($request);
    }
}
