<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Entry;
use App\Models\Folder;
use App\Models\User;

class EntryPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Entry $entry): bool
    {
        // Everybody can view an unrestricted entry.
        if (! $entry->isRestricted) {
            return true;
        }

        // A guest user can't view a restricted entry.
        if (is_null($user) || $user->isPending) {
            return false;
        }

        // A user can view a restricted entry if it's in their entries list and
        // they can view the parent folder.
        if ($user->entries->contains($entry) && $user->can('view', $entry->folder)) {
            return true;
        }

        // An admin user can view everything.
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Folder $folder): bool
    {
        return $user->isAdmin && ! $folder->isRoot;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Entry $entry): bool
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Entry $entry): bool
    {
        return $user->isAdmin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Entry $entry): bool
    {
        return $user->isAdmin && $entry->trashed();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Entry $entry): bool
    {
        return $user->isAdmin && $entry->trashed();
    }
}
