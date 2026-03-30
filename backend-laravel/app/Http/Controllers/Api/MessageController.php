<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Demande;
use App\Models\Message;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Liste des messages d'une demande.
     * Accessible : citoyen propriétaire, agent assigné, admin.
     */
    public function index(Request $request, int $demandeId): JsonResponse
    {
        $user    = $request->user();
        $demande = Demande::findOrFail($demandeId);

        $this->checkDemandeAccess($user, $demande);

        $messages = Message::with('sender:id,name,role')
            ->where('demande_id', $demandeId)
            ->orderBy('created_at')
            ->get();

        // Marquer comme lus les messages reçus par cet utilisateur
        Message::where('demande_id', $demandeId)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json($messages);
    }

    /**
     * Envoyer un message sur une demande.
     */
    public function store(Request $request, int $demandeId): JsonResponse
    {
        $user    = $request->user();
        $demande = Demande::findOrFail($demandeId);

        $this->checkDemandeAccess($user, $demande);

        // Déterminer le destinataire
        if ($user->isCitoyen()) {
            // Le citoyen répond à l'agent assigné (ou à un admin si pas d'agent)
            $receiverId = $demande->agent_id ?? $this->getAnyAdmin();
            if (! $receiverId) {
                return response()->json(['message' => 'Aucun agent assigné à cette demande pour l\'instant.'], 422);
            }
        } else {
            // Agent ou admin répond au citoyen
            $receiverId = $demande->user_id;
            if (! $receiverId) {
                return response()->json(['message' => 'Aucun compte citoyen associé à cette demande.'], 422);
            }
        }

        $payload = $request->validate([
            'body'        => ['required', 'string', 'max:5000'],
            'receiver_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        // Admin peut choisir le destinataire manuellement
        if ($user->isAdmin() && isset($payload['receiver_id'])) {
            $receiverId = $payload['receiver_id'];
        }

        $message = Message::create([
            'demande_id'  => $demandeId,
            'sender_id'   => $user->id,
            'receiver_id' => $receiverId,
            'body'        => $payload['body'],
        ]);

        ActivityLogger::log(
            'send_message',
            $user->id,
            Demande::class,
            $demandeId,
            ['message_id' => $message->id],
            $request
        );

        return response()->json($message->load('sender:id,name,role'), 201);
    }

    /**
     * Nombre de messages non lus pour l'utilisateur connecté.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = Message::where('receiver_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['unread_count' => $count]);
    }

    // ---
    private function checkDemandeAccess(\App\Models\User $user, Demande $demande): void
    {
        if ($user->isCitoyen() && $demande->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }
        if ($user->isAgent() && $demande->agent_id !== $user->id) {
            abort(403, 'Cette demande ne vous est pas assignée.');
        }
    }

    private function getAnyAdmin(): ?int
    {
        return \App\Models\User::where('role', 'admin')->value('id');
    }
}
