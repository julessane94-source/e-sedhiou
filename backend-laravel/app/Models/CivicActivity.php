<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CivicActivity extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'icon_emoji',
        'content',
        'event_date',
        'event_start_time',
        'event_end_time',
        'location',
        'location_details',
        'target_audience',
        'max_participants',
        'activity_details',
        'status',
        'activity_type',
        'frequency',
        'is_active',
        'sort_order',
        'created_by',
        'updated_by',
        'document_path',
        'image_path',
        'document_name',
        'image_name',
    ];

    protected function casts(): array
    {
        return [
            'activity_details' => 'array',
            'event_date' => 'datetime',
            'event_start_time' => 'datetime:H:i',
            'event_end_time' => 'datetime:H:i',
            'is_active' => 'boolean',
        ];
    }

    // Relations
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')->active();
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('event_date');
    }

    // Helpers
    public function getStatusLabel(): string
    {
        $labels = [
            'upcoming' => 'À venir',
            'ongoing' => 'En cours',
            'completed' => 'Complétée',
            'cancelled' => 'Annulée',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getFrequencyLabel(): string
    {
        $labels = [
            'once' => 'Unique',
            'weekly' => 'Hebdomadaire',
            'monthly' => 'Mensuel',
            'quarterly' => 'Trimestriel',
        ];
        return $labels[$this->frequency] ?? $this->frequency;
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'community' => 'Communautaire',
            'workshop' => 'Atelier',
            'forum' => 'Forum',
            'celebration' => 'Célébration',
        ];
        return $labels[$this->activity_type] ?? $this->activity_type;
    }
}
