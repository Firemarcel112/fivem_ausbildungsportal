<?php

namespace App\Http\Controllers;

use App\DTO\UserProfileViewData;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProfileController extends Controller
{
    use AuthorizesRequests;

    /**
     * Anzeige eines Profils
     *
     * @param  User  $user
     * @return mixed
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $profile = UserProfileViewData::fromModel($user);

        return view('user.profile', compact('profile'));
    }
}
