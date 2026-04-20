<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        $has_permission = $user->can('usermanagement.index', [0, 'ignore-superadmin']);

        $is_my_profile = $user->getKey() == $model->getKey();

        return $has_permission || $is_my_profile;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('usermanagement.store');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {

        $user_is_superadmin = $model->isSuperadmin();

        $auth_user_is_superadmin = $user->isSuperadmin();

        $can_any = $user->canAny([
            'usermanagement.edit.account_data',
            'usermanagement.edit.personal_data',
            'usermanagement.edit.permissions',
        ]);

        return $auth_user_is_superadmin || (!$user_is_superadmin && $can_any);
    }

    public function updateSettings(User $user, User $model): bool
    {
        $is_my_profile = $user->getKey() == $model->getKey();

        return $is_my_profile;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        return false;
    }
}
