<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(
        string $action,
        ?int $userId = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
        array $metadata = [],
        ?Request $request = null
    ): void {
        ActivityLog::create([
            'user_id'      => $userId,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'metadata'     => empty($metadata) ? null : $metadata,
            'ip_address'   => $request?->ip(),
            'user_agent'   => $request ? substr((string) $request->userAgent(), 0, 500) : null,
            'created_at'   => now(),
        ]);
    }
}
