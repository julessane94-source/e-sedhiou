<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WpContactMessage;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WordPressContactMessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'wp_contact_message_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:190'],
            'email' => ['required', 'email:rfc', 'max:190'],
            'subject' => ['nullable', 'string', 'max:190'],
            'message' => ['required', 'string', 'max:10000'],
            'source' => ['nullable', 'string', 'max:80'],
            'source_url' => ['nullable', 'url', 'max:2048'],
            'sender_ip' => ['nullable', 'string', 'max:64'],
            'user_agent' => ['nullable', 'string', 'max:1000'],
            'received_at' => ['nullable', 'date'],
        ]);

        $contactMessage = null;
        if (! empty($payload['wp_contact_message_id'])) {
            $contactMessage = WpContactMessage::where('wp_contact_message_id', (int) $payload['wp_contact_message_id'])->first();
        }

        if (! $contactMessage) {
            $contactMessage = new WpContactMessage();
            $contactMessage->wp_contact_message_id = ! empty($payload['wp_contact_message_id'])
                ? (int) $payload['wp_contact_message_id']
                : null;
        }

        $contactMessage->sender_name = (string) $payload['name'];
        $contactMessage->sender_email = (string) $payload['email'];
        $contactMessage->subject = isset($payload['subject']) && trim((string) $payload['subject']) !== ''
            ? (string) $payload['subject']
            : null;
        $contactMessage->message = (string) $payload['message'];
        $contactMessage->source = (string) ($payload['source'] ?? 'wordpress');
        $contactMessage->source_url = isset($payload['source_url']) && trim((string) $payload['source_url']) !== ''
            ? (string) $payload['source_url']
            : null;
        $contactMessage->sender_ip = isset($payload['sender_ip']) && trim((string) $payload['sender_ip']) !== ''
            ? (string) $payload['sender_ip']
            : null;
        $contactMessage->user_agent = isset($payload['user_agent']) && trim((string) $payload['user_agent']) !== ''
            ? (string) $payload['user_agent']
            : null;
        $contactMessage->received_at = $payload['received_at'] ?? now();

        $isNew = ! $contactMessage->exists;
        $contactMessage->save();

        ActivityLogger::log(
            'wp_contact_message_received',
            null,
            WpContactMessage::class,
            $contactMessage->id,
            [
                'source' => $contactMessage->source,
                'sender_email' => $contactMessage->sender_email,
                'wp_contact_message_id' => $contactMessage->wp_contact_message_id,
            ],
            $request
        );

        return response()->json([
            'id' => $contactMessage->id,
            'status' => $isNew ? 'created' : 'updated',
        ], $isNew ? 201 : 200);
    }
}
