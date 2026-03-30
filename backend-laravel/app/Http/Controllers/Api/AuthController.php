<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Inscription d'un nouveau citoyen (ou agent/admin si token admin fourni).
     */
    public function register(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password'   => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],
            'phone'      => ['nullable', 'string', 'max:30'],
            'address'    => ['nullable', 'string', 'max:500'],
            // Seul un admin peut créer agent/admin via l'header spécial
            'role'       => ['nullable', 'in:citoyen,agent,admin'],
        ]);

        // Autoriser rôle non-citoyen seulement si l'appelant est admin authentifié
        $role = 'citoyen';
        if (isset($payload['role']) && $payload['role'] !== 'citoyen') {
            $caller = JWTAuth::setRequest($request)->parseToken()->authenticate();
            if (! $caller || ! $caller->isAdmin()) {
                return response()->json(['message' => 'Seul un administrateur peut créer un compte agent ou admin.'], 403);
            }
            $role = $payload['role'];
        }

        $user = User::create([
            'name'       => $payload['name'],
            'first_name' => $payload['first_name'] ?? null,
            'last_name'  => $payload['last_name'] ?? null,
            'email'      => $payload['email'],
            'password'   => Hash::make($payload['password']),
            'role'       => $role,
            'phone'      => $payload['phone'] ?? null,
            'address'    => $payload['address'] ?? null,
        ]);

        ActivityLogger::log('register', $user->id, User::class, $user->id, ['role' => $role], $request);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Compte créé avec succès.',
            'user'    => $this->userResource($user),
            'token'   => $token,
        ], 201);
    }

    /**
     * Connexion : retourne un token JWT.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects.'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Compte désactivé. Contactez l\'administration.'], 403);
        }

        $token = JWTAuth::fromUser($user);
        $user->update(['last_login_at' => now()]);

        ActivityLogger::log('login', $user->id, null, null, [], $request);

        return response()->json([
            'message' => 'Connexion réussie.',
            'user'    => $this->userResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * Emission d'un JWT technique pour la synchronisation WordPress -> Laravel
     * via le middleware mairie.token.
     */
    public function issueSyncToken(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $payload['email'])->first();

        if (! $user) {
            return response()->json(['message' => 'Utilisateur introuvable.'], 404);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Compte désactivé.'], 403);
        }

        $token = JWTAuth::fromUser($user);

        ActivityLogger::log('sync_issue_token', $user->id, User::class, $user->id, ['source' => 'wordpress'], $request);

        return response()->json([
            'message' => 'Token de synchronisation émis.',
            'user'    => $this->userResource($user),
            'token'   => $token,
        ]);
    }

    /**
     * Déconnexion : invalide le token JWT.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        JWTAuth::invalidate(JWTAuth::getToken());
        ActivityLogger::log('logout', $user?->id, null, null, [], $request);

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * Retourne le profil de l'utilisateur connecté.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => $this->userResource($request->user())]);
    }

    /**
     * Mise à jour du profil (l'utilisateur connecté).
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $payload = $request->validate([
            'name'         => ['sometimes', 'string', 'max:150'],
            'first_name'   => ['nullable', 'string', 'max:120'],
            'last_name'    => ['nullable', 'string', 'max:120'],
            'phone'        => ['nullable', 'string', 'max:30'],
            'address'      => ['nullable', 'string', 'max:500'],
            'password'     => ['nullable', 'confirmed', Password::min(8)],
        ]);

        if (isset($payload['password'])) {
            $payload['password'] = Hash::make($payload['password']);
        } else {
            unset($payload['password']);
        }

        $user->update($payload);
        ActivityLogger::log('update_profile', $user->id, User::class, $user->id, [], $request);

        return response()->json([
            'message' => 'Profil mis à jour.',
            'user'    => $this->userResource($user->fresh()),
        ]);
    }

    /**
     * Refresh le token JWT.
     */
    public function refresh(): JsonResponse
    {
        $newToken = JWTAuth::refresh(JWTAuth::getToken());
        return response()->json(['token' => $newToken]);
    }

    // ---
    private function userResource(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'role'       => $user->role,
            'phone'      => $user->phone,
            'address'    => $user->address,
            'is_active'  => $user->is_active,
            'created_at' => $user->created_at?->toDateTimeString(),
        ];
    }
}
