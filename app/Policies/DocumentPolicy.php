<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('documents.show.account');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Document $document): bool
    {
        $document->loadMissing('documentAssign');
        $linked_document = $document->documentAssign;
        $documents_edit_permission = $user->can('documents.edit');

        if (empty($linked_document)) {
            return $documents_edit_permission;
        }

        $link_type = strtolower($linked_document->link_type);
        $is_own_file = $user->account?->getKey() == $linked_document->link_id;
        $has_show_permission = $user->can('documents.show.' . $link_type);

        return ($link_type == 'account' && ($is_own_file || $has_show_permission)) || $documents_edit_permission;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('documents.create');
    }

    /**
     * Determine whether the user can update any model.
     */
    public function updateAny(User $user): bool
    {
        return $user->can('documents.edit');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Document $document): bool
    {
        return $user->can('documents.edit.' . $document->getKey());
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->can('documents.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return false;
    }
}
