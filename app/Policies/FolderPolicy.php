<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

class FolderPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // An admin can do everything.
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Folder $folder): bool
    {
        // Everybody can view an unrestricted folder.
        if (! $folder->isRestricted) {
            return true;
        }

        // A guest user can't view a restricted folder.
        if (is_null($user) || $user->role === 'pending') {
            return false;
        }

        // A user can view a restricted folder if it's in their folders list and
        // they can view the parent folder.
        if ($user->folders->contains($folder) && $user->can('view', $folder->folder)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Folder $folder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Folder $folder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Folder $folder): bool
    {
        return false;
    }
}
