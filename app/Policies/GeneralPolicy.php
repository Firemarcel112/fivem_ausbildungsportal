<?php

namespace App\Policies;

use App\Models\User;

class GeneralPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Prüft ob der Eingeloggte User Ausbilder ist
     *
     * @param \App\Models\User $user
     * @return bool
     */
    public function isTrainer(User $user)
    {
        return $user->hasDirectPermission('is_trainer');
    }
}
