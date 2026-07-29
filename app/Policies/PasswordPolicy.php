<?php

namespace App\Policies;

use App\Models\Password;
use App\Models\User;

class PasswordPolicy
{
    /**
     * Determine if the user can view the password.
     * All employees can view all company passwords.
     */
    public function view(User $user, Password $password): bool
    {
        return true;
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
     * All employees can update company passwords.
     */
    public function update(User $user, Password $password): bool
    {
        return true;
    }

    /**
     * Determine if the user can delete the password.
     * All employees can delete company passwords.
     */
    public function delete(User $user, Password $password): bool
    {
        return true;
    }
}
