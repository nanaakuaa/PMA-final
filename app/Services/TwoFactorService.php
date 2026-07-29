<?php

namespace App\Services;

use App\Models\User;
use App\Models\TwoFactorAuthentication;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Str;

class TwoFactorService
{
    protected Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQrCode(User $user, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
    }

    public function verifyCode(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, $code);
    }

    public function generateRecoveryCodes(): array
    {
        return array_map(function () {
            return Str::random(8);
        }, range(1, 10));
    }

    public function enable(User $user, string $secret): TwoFactorAuthentication
    {
        return $user->twoFactorAuthentication()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'secret' => $secret,
                'recovery_codes' => $this->generateRecoveryCodes(),
                'is_enabled' => true,
                'enabled_at' => now(),
            ]
        );
    }

    public function disable(User $user): void
    {
        $user->twoFactorAuthentication()->update([
            'is_enabled' => false,
        ]);
    }

    public function useRecoveryCode(User $user, string $code): bool
    {
        $twoFactor = $user->twoFactorAuthentication;
        if (!$twoFactor || !$twoFactor->is_enabled) {
            return false;
        }

        $codes = $twoFactor->recovery_codes;
        $key = array_search($code, $codes);

        if ($key === false) {
            return false;
        }

        unset($codes[$key]);
        $twoFactor->update(['recovery_codes' => array_values($codes)]);

        return true;
    }
}
