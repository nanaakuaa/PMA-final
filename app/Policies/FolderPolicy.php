<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

class FolderPolicy
{
    /**
     * Determine if the user can view the folder.
     * All employees can view all company folders.
     */
    public function view(User $user, Folder $folder): bool
    {
        return true;
    }

    /**
     * Determine if the user can create folders.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the folder.
     * All employees can update company folders.
     */
    public function update(User $user, Folder $folder): bool
    {
        return true;
    }

    /**
     * Determine if the user can delete the folder.
     * All employees can delete company folders.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return true;
    }
}
