<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;

class FolderPolicy
{
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
        if (is_null($user) || $user->isPending) {
            return false;
        }

        // A user can view a restricted folder if it's in their folders list and
        // they can view the parent folder.
        if ($user->folders->contains($folder) && $user->can('view', $folder->folder)) {
            return true;
        }

        // An admin user can view everything.
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Folder $folder): bool
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return $user->isAdmin && ! $folder->isRoot;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Folder $folder): bool
    {
        return $user->isAdmin && $folder->trashed();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Folder $folder): bool
    {
        return $user->isAdmin && $folder->trashed();
    }
}
