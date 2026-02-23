<?php

namespace App\Services;

use App\Models\ApplicationAuditLog;
use Illuminate\Support\Facades\Auth;

class ApplicationAuditLogger
{
    public function log(
        string $actionType,
        ?int $userId = null,
        ?string $entityType = null,
        int|string|null $entityId = null,
        ?string $description = null,
        ?array $metadata = null
    ): void {
        ApplicationAuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action_type' => $actionType,
            'entity_type' => $entityType,
            'entity_id' => is_numeric($entityId) ? (int) $entityId : null,
            'description' => $description,
            'metadata' => $metadata,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }
}
