<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Folder;
use App\Services\EncryptionService;
use Illuminate\Database\Eloquent\Factories\Factory;

class PasswordFactory extends Factory
{
    public function definition(): array
    {
        $encryptionService = app(EncryptionService::class);

        return [
            'user_id' => User::factory(),
            'folder_id' => null,
            'title' => fake()->words(2, true),
            'username' => fake()->userName(),
            'password' => $encryptionService->encrypt(fake()->password(12)),
            'url' => fake()->url(),
            'notes' => fake()->boolean(30) ? $encryptionService->encrypt(fake()->sentence()) : null,
        ];
    }
}
