<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Demande;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function __construct()
    {
        // Tous ces endpoints exigent le rôle admin
    }

    /**
     * Liste tous les utilisateurs avec filtre par rôle.
     */
    public function listUsers(Request $request): JsonResponse
    {
        $role  = $request->query('role');
        $query = User::query();

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->orderByDesc('created_at')->paginate(20);

        return response()->json($users);
    }

    /**
     * Créer un agent ou admin (seul un admin peut faire ça).
     */
    public function createUser(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'first_name' => ['nullable', 'string', 'max:120'],
            'last_name'  => ['nullable', 'string', 'max:120'],
            'email'      => ['required', 'email:rfc', 'max:190', 'unique:users,email'],
            'password'   => ['required', Password::min(8)],
            'role'       => ['required', 'in:agent,superviseur,admin,citoyen'],
            'phone'      => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            ...$payload,
            'password' => Hash::make($payload['password']),
        ]);

        ActivityLogger::log('admin_create_user', $request->user()->id, User::class, $user->id, ['role' => $user->role], $request);

        return response()->json(['message' => 'Compte créé.', 'user' => $user], 201);
    }

    /**
     * Activer / désactiver un compte utilisateur.
     */
    public function toggleUser(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas désactiver votre propre compte.'], 422);
        }

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLogger::log(
            $user->is_active ? 'admin_activate_user' : 'admin_deactivate_user',
            $request->user()->id,
            User::class,
            $user->id,
            [],
            $request
        );

        return response()->json([
            'message'   => $user->is_active ? 'Compte activé.' : 'Compte désactivé.',
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * Modifier le rôle d'un utilisateur.
     */
    public function changeRole(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Vous ne pouvez pas modifier votre propre rôle.'], 422);
        }

        $payload = $request->validate([
            'role' => ['required', 'in:admin,agent,superviseur,citoyen'],
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $payload['role']]);

        ActivityLogger::log(
            'admin_change_role',
            $request->user()->id,
            User::class,
            $user->id,
            ['old_role' => $oldRole, 'new_role' => $payload['role']],
            $request
        );

        return response()->json(['message' => 'Rôle mis à jour.', 'user' => $user]);
    }

    /**
     * Supprimer un utilisateur (soft: désactiver ; vraie suppression si force=1).
     */
    public function deleteUser(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json(['message' => 'Impossible de supprimer son propre compte.'], 422);
        }

        ActivityLogger::log('admin_delete_user', $request->user()->id, User::class, $user->id, ['email' => $user->email], $request);
        $user->delete();

        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    /**
     * Dashboard statistiques globales.
     */
    public function dashboard(Request $request): JsonResponse
    {
        return response()->json([
            'users' => [
                'total'   => User::count(),
                'admins'  => User::where('role', 'admin')->count(),
                'agents'  => User::where('role', 'agent')->count(),
                'superviseurs' => User::where('role', 'superviseur')->count(),
                'citoyens'=> User::where('role', 'citoyen')->count(),
                'active'  => User::where('is_active', true)->count(),
            ],
            'demandes' => [
                'total'      => Demande::count(),
                'pending'    => Demande::where('status', 'pending')->count(),
                'assigned'   => Demande::where('status', 'assigned')->count(),
                'processing' => Demande::where('status', 'processing')->count(),
                'completed'  => Demande::where('status', 'completed')->count(),
                'rejected'   => Demande::where('status', 'rejected')->count(),
            ],
            'recent_activity' => ActivityLog::with('user:id,name,role')
                ->orderByDesc('created_at')
                ->limit(20)
                ->get(),
        ]);
    }

    /**
     * Journal d'activité complet paginé.
     */
    public function activityLog(Request $request): JsonResponse
    {
        $logs = ActivityLog::with('user:id,name,role')
            ->when($request->query('user_id'), fn ($q, $uid) => $q->where('user_id', $uid))
            ->when($request->query('action'),  fn ($q, $a)   => $q->where('action', $a))
            ->orderByDesc('created_at')
            ->paginate(30);

        return response()->json($logs);
    }
}
