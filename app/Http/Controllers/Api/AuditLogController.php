<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 50);

        $logs = AuditLog::query()
            ->when($request->action, fn($q) => $q->where('action', $request->action))
            ->when($request->model_type, fn($q) => $q->where('model_type', $request->model_type))
            ->with('user')
            ->latest()
            ->paginate($perPage);

        return response()->json($logs);
    }

    public function show(AuditLog $auditLog)
    {
        $this->authorize('view', $auditLog);

        return response()->json($auditLog);
    }
}
