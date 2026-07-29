<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Password;
use App\Services\AuditService;
use Illuminate\Http\Request;

class PasswordShareController extends Controller
{
    public function __construct(
        private AuditService $auditService
    ) {}

    public function share(Request $request, Password $password)
    {
        $this->authorize('view', $password);

        $validated = $request->validate([
            'email' => 'required|email',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Generate a secure shareable link
        $token = bin2hex(random_bytes(32));

        $password->shares()->create([
            'shared_with_email' => $validated['email'],
            'token' => $token,
            'expires_at' => $validated['expires_at'] ?? now()->addDays(7),
        ]);

        $this->auditService->log('password_shared', $password, [
            'shared_with' => $validated['email']
        ]);

        return response()->json([
            'message' => 'Password shared successfully',
            'share_link' => url("/shared/{$token}")
        ]);
    }

    public function revoke(Password $password, $shareId)
    {
        $this->authorize('update', $password);

        $share = $password->shares()->findOrFail($shareId);
        $share->delete();

        $this->auditService->log('password_share_revoked', $password);

        return response()->json(['message' => 'Share revoked successfully']);
    }
}
