<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentAttendance extends Model
{
    public const STATUS_PRESENT = 'present';
    public const STATUS_LATE = 'late';
    public const STATUS_ABSENT = 'absent';

    protected $fillable = [
        'user_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'notes',
        'marked_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
