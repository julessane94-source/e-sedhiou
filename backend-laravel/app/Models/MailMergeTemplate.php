<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MailMergeTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'request_type',
        'file_path',
        'original_name',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}