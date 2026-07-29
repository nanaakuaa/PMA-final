<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class EncryptionService
{
    /**
     * Encrypt sensitive data
     */
    public function encrypt(string $data): string
    {
        return Crypt::encryptString($data);
    }

    /**
     * Decrypt sensitive data
     */
    public function decrypt(string $encryptedData): string
    {
        try {
            return Crypt::decryptString($encryptedData);
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to decrypt data: ' . $e->getMessage());
        }
    }

    /**
     * Re-encrypt data (useful for key rotation)
     */
    public function reEncrypt(string $encryptedData): string
    {
        $decrypted = $this->decrypt($encryptedData);
        return $this->encrypt($decrypted);
    }
}
