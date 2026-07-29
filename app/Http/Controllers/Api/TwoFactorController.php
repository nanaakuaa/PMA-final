<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;

class TwoFactorController extends Controller
{
    public function __construct(
        private TwoFactorService $twoFactorService
    ) {}

    public function status(Request $request)
    {
        $user = $request->user();
        $twoFactor = $user->twoFactorAuthentication;

        return response()->json([
            'is_enabled' => $twoFactor?->is_enabled ?? false,
            'enabled_at' => $twoFactor?->enabled_at,
        ]);
    }

    public function generateSetup(Request $request)
    {
        $user = $request->user();
        $secret = $this->twoFactorService->generateSecret();
        $qrCode = $this->twoFactorService->getQrCode($user, $secret);

        return response()->json([
            'secret' => $secret,
            'qr_code' => $qrCode,
        ]);
    }

    public function verifyAndEnable(Request $request)
    {
        $validated = $request->validate([
            'secret' => 'required|string',
            'code' => 'required|string|size:6',
        ]);

        if (!$this->twoFactorService->verifyCode($validated['secret'], $validated['code'])) {
            return response()->json(['message' => 'Invalid verification code'], 422);
        }

        $user = $request->user();
        $this->twoFactorService->enable($user, $validated['secret']);

        $twoFactor = $user->twoFactorAuthentication;

        return response()->json([
            'message' => '2FA enabled successfully',
            'recovery_codes' => $twoFactor->recovery_codes,
        ]);
    }

    public function disable(Request $request)
    {
        $validated = $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (!\Illuminate\Support\Facades\Hash::check($validated['password'], $user->password)) {
            return response()->json(['message' => 'Invalid password'], 422);
        }

        $this->twoFactorService->disable($user);

        return response()->json(['message' => '2FA disabled successfully']);
    }

    public function verifyCode(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
        ]);

        $user = $request->user();
        $twoFactor = $user->twoFactorAuthentication;

        if (!$twoFactor || !$twoFactor->is_enabled) {
            return response()->json(['message' => '2FA not enabled'], 422);
        }

        // Try OTP code first
        if ($this->twoFactorService->verifyCode($twoFactor->secret, $validated['code'])) {
            return response()->json(['message' => 'Code verified', 'valid' => true]);
        }

        // Try recovery code
        if ($this->twoFactorService->useRecoveryCode($user, $validated['code'])) {
            return response()->json(['message' => 'Recovery code used', 'valid' => true]);
        }

        return response()->json(['message' => 'Invalid code'], 422);
    }
}
