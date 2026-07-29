<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an audit event
     */
    public function log(
        string $action,
        ?Model $model = null,
        array $metadata = []
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model?->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get recent activity for a user
     */
    public function getRecentActivity(int $userId, int $limit = 10)
    {
        return AuditLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get audit logs for a specific model
     */
    public function getModelLogs(Model $model, int $limit = 20)
    {
        return AuditLog::where('model_type', get_class($model))
            ->where('model_id', $model->id)
            ->with('user')
            ->latest()
            ->take($limit)
            ->get();
    }
}
