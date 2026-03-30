<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WpContactMessage extends Model
{
    protected $fillable = [
        'wp_contact_message_id',
        'sender_name',
        'sender_email',
        'subject',
        'message',
        'source',
        'source_url',
        'sender_ip',
        'user_agent',
        'received_at',
        'replied_at',
        'reply_subject',
        'reply_body',
        'reply_status',
        'reply_error',
        'replied_by',
    ];

    protected function casts(): array
    {
        return [
            'received_at' => 'datetime',
            'replied_at' => 'datetime',
        ];
    }

    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }
}
