<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Determine if the user can view the audit log.
     * All employees can view all audit logs for transparency.
     */
    public function view(User $user, AuditLog $auditLog): bool
    {
        return true;
    }

    /**
     * Determine if the user can view any audit logs.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }
}
