<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    public const STATUS_PENDING    = 'pending';
    public const STATUS_ASSIGNED   = 'assigned';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED  = 'completed';
    public const STATUS_REJECTED   = 'rejected';

    protected $fillable = [
        'user_id',
        'agent_id',
        'reference',
        'request_type',
        'email',
        'first_name',
        'last_name',
        'birth_date',
        'birth_place',
        'register_number',
        'address',
        'parent_one_first_name',
        'parent_one_last_name',
        'parent_two_first_name',
        'parent_two_last_name',
        'details',
        'agent_notes',
        'attachment_url',
        'attachment_name',
        'status',
        'payment_status',
        'payment_reference',
        'paid_at',
        'payment_validated_by',
        'payment_validated_at',
        'source',
        'wp_request_id',
        'assigned_at',
        'processed_at',
        'processing_channel',
        'processed_document_path',
        'processed_document_name',
        // Champs professionnels
        'profession_sector',
        'profession_title',
        'education_level',
        'years_experience',
        'skills',
        'current_status',
        'available_for_municipality',
        'cv_file_path',
        'cv_file_name',
        'portfolio_url',
    ];

    protected function casts(): array
    {
        return [
            'birth_date'   => 'date',
            'paid_at'      => 'datetime',
            'payment_validated_at' => 'datetime',
            'assigned_at'  => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    // --- Relations ---
    public function citoyen(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'demande_id')->orderBy('created_at');
    }
}
