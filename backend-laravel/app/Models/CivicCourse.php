<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CivicCourse extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'icon_emoji',
        'content',
        'duration_minutes',
        'topics',
        'is_active',
        'course_type',
        'difficulty_level',
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
            'topics' => 'array',
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

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    // Helpers
    public function topicsArray(): array
    {
        return is_array($this->topics) ? $this->topics : [];
    }
}
