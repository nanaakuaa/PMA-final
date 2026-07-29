<?php

namespace App\Policies;

use App\Models\Password;
use App\Models\User;

class PasswordPolicy
{
    /**
     * Determine if the user can view the password.
     * Users can only view passwords they own.
     */
    public function view(User $user, Password $password): bool
    {
        return $password->user_id === $user->id;
    }

    /**
     * Determine if the user can create passwords.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the password.
     * Users can only update passwords they own.
     */
    public function update(User $user, Password $password): bool
    {
        return $password->user_id === $user->id;
    }

    /**
     * Determine if the user can delete the password.
     * Users can only delete passwords they own.
     */
    public function delete(User $user, Password $password): bool
    {
        return $password->user_id === $user->id;
    }
}
