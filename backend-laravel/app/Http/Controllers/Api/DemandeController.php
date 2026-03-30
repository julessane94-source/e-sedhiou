<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DemandeController extends Controller
{
    /**
     * Liste des demandes selon le rôle de l'utilisateur :
     * - citoyen : ses propres demandes
     * - agent   : les demandes qui lui sont assignées
     * - admin   : toutes les demandes
     */
    public function index(Request $request): JsonResponse
    {
        $user  = $request->user();
        $query = Demande::with(['citoyen:id,name,email', 'agent:id,name,email']);

        if ($user->isCitoyen()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isAgent()) {
            $query->where('agent_id', $user->id);
        }
        // admin : tout sans filtre

        $status = $request->query('status');
        if ($status) {
            $query->where('status', $status);
        }

        $demandes = $query->orderByDesc('created_at')->paginate(15);

        return response()->json($demandes);
    }

    /**
     * Détail d'une demande (avec ses messages).
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $demande = Demande::with(['citoyen:id,name,email', 'agent:id,name,email', 'messages.sender:id,name,role'])->findOrFail($id);

        $this->authorizeAccess($user, $demande);

        return response()->json($demande);
    }

    /**
     * Création d'une demande par un citoyen (ou depuis WordPress via mairie.token).
     */
    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'request_type'           => ['required', 'string', 'max:120'],
            'email'                  => ['required', 'email:rfc', 'max:190'],
            'first_name'             => ['required', 'string', 'max:120'],
            'last_name'              => ['required', 'string', 'max:120'],
            'birth_date'             => ['required', 'date'],
            'birth_place'            => ['required', 'string', 'max:190'],
            'register_number'        => ['required', 'string', 'max:120'],
            'address'                => ['required', 'string', 'max:1000'],
            'parent_one_first_name'  => ['required', 'string', 'max:120'],
            'parent_one_last_name'   => ['required', 'string', 'max:120'],
            'parent_two_first_name'  => ['required', 'string', 'max:120'],
            'parent_two_last_name'   => ['required', 'string', 'max:120'],
            'details'                => ['nullable', 'string', 'max:4000'],
            'attachment_url'         => ['nullable', 'url', 'max:2000'],
            'attachment_name'        => ['nullable', 'string', 'max:255'],
            'source'                 => ['nullable', 'string', 'max:80'],
            'wp_request_id'          => ['nullable', 'integer'],
        ]);

        $payload['reference'] = 'REQ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $payload['status']    = Demande::STATUS_PENDING;

        // Si l'appelant est un citoyen authentifié, on lie la demande à son compte
        if ($request->user()) {
            $payload['user_id'] = $request->user()->id;
        }

        $demande = Demande::create($payload);

        ActivityLogger::log(
            'create_demande',
            $request->user()?->id,
            Demande::class,
            $demande->id,
            ['reference' => $demande->reference, 'source' => $demande->source],
            $request
        );

        return response()->json([
            'id'        => $demande->id,
            'reference' => $demande->reference,
            'status'    => $demande->status,
        ], 201);
    }

    /**
     * Un agent assigne une demande à lui-même (ou admin l'assigne à un agent).
     */
    public function assign(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $demande = Demande::findOrFail($id);

        if (! $user->isAgent() && ! $user->isAdmin()) {
            return response()->json(['message' => 'Action réservée aux agents et administrateurs.'], 403);
        }

        if ($demande->status !== Demande::STATUS_PENDING) {
            return response()->json(['message' => 'Cette demande est déjà prise en charge.'], 422);
        }

        $agentId = $user->isAdmin()
            ? ($request->input('agent_id', $user->id))
            : $user->id;

        // Vérifie que l'agent cible est bien un agent
        if ($user->isAdmin() && $agentId !== $user->id) {
            $agent = User::where('id', $agentId)->where('role', User::ROLE_AGENT)->first();
            if (! $agent) {
                return response()->json(['message' => 'Agent introuvable.'], 404);
            }
        }

        $demande->update([
            'agent_id'    => $agentId,
            'status'      => Demande::STATUS_ASSIGNED,
            'assigned_at' => now(),
        ]);

        ActivityLogger::log('assign_demande', $user->id, Demande::class, $demande->id, ['agent_id' => $agentId], $request);

        return response()->json(['message' => 'Demande assignée.', 'demande' => $demande->fresh()]);
    }

    /**
     * L'agent met à jour le statut et ajoute ses notes.
     */
    public function process(Request $request, int $id): JsonResponse
    {
        $user    = $request->user();
        $demande = Demande::findOrFail($id);

        if (! $user->isAgent() && ! $user->isAdmin()) {
            return response()->json(['message' => 'Action réservée aux agents et administrateurs.'], 403);
        }

        if ($user->isAgent() && $demande->agent_id !== $user->id) {
            return response()->json(['message' => 'Vous n\'êtes pas assigné à cette demande.'], 403);
        }

        $payload = $request->validate([
            'status'      => ['required', 'in:processing,completed,rejected'],
            'agent_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $update = [
            'status'      => $payload['status'],
            'agent_notes' => $payload['agent_notes'] ?? $demande->agent_notes,
        ];

        if (in_array($payload['status'], [Demande::STATUS_COMPLETED, Demande::STATUS_REJECTED])) {
            $update['processed_at'] = now();
        }

        $demande->update($update);

        ActivityLogger::log(
            'process_demande',
            $user->id,
            Demande::class,
            $demande->id,
            ['new_status' => $payload['status']],
            $request
        );

        return response()->json(['message' => 'Demande mise à jour.', 'demande' => $demande->fresh()]);
    }

    /**
     * Admin : liste des stats globales.
     */
    public function stats(Request $request): JsonResponse
    {
        if (! $request->user()->isAdmin()) {
            return response()->json(['message' => 'Réservé aux administrateurs.'], 403);
        }

        return response()->json([
            'total'      => Demande::count(),
            'pending'    => Demande::where('status', Demande::STATUS_PENDING)->count(),
            'assigned'   => Demande::where('status', Demande::STATUS_ASSIGNED)->count(),
            'processing' => Demande::where('status', Demande::STATUS_PROCESSING)->count(),
            'completed'  => Demande::where('status', Demande::STATUS_COMPLETED)->count(),
            'rejected'   => Demande::where('status', Demande::STATUS_REJECTED)->count(),
        ]);
    }

    // --- Accès : citoyen voit ses demandes, agent les siennes, admin tout ---
    private function authorizeAccess(User $user, Demande $demande): void
    {
        if ($user->isCitoyen() && $demande->user_id !== $user->id) {
            abort(403, 'Accès non autorisé à cette demande.');
        }

        if ($user->isAgent() && $demande->agent_id !== $user->id) {
            abort(403, 'Cette demande n\'est pas assignée à votre compte.');
        }
    }
}

