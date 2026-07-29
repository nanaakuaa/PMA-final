<?php

namespace App\Services;

class PasswordGeneratorService
{
    /**
     * Generate a secure random password
     */
    public function generate(
        int $length = 16,
        bool $includeUppercase = true,
        bool $includeLowercase = true,
        bool $includeNumbers = true,
        bool $includeSymbols = true
    ): string {
        $characters = '';

        if ($includeLowercase) {
            $characters .= 'abcdefghijklmnopqrstuvwxyz';
        }

        if ($includeUppercase) {
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if ($includeNumbers) {
            $characters .= '0123456789';
        }

        if ($includeSymbols) {
            $characters .= '!@#$%^&*()-_=+[]{}|;:,.<>?';
        }

        if (empty($characters)) {
            throw new \InvalidArgumentException('At least one character type must be selected');
        }

        $password = '';
        $charactersLength = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $password;
    }

    /**
     * Calculate password strength (0-100)
     */
    public function calculateStrength(string $password): int
    {
        $strength = 0;
        $length = strlen($password);

        // Length check
        if ($length >= 8) $strength += 20;
        if ($length >= 12) $strength += 10;
        if ($length >= 16) $strength += 10;

        // Character variety
        if (preg_match('/[a-z]/', $password)) $strength += 15;
        if (preg_match('/[A-Z]/', $password)) $strength += 15;
        if (preg_match('/[0-9]/', $password)) $strength += 15;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength += 15;

        return min(100, $strength);
    }
}
