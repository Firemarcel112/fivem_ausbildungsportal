<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationService
{
    public function attemptLogin(
        string $username,
        string $password,
        bool $remember = false,
    ): bool {
        $user = $this->getUserByUsername($username);

        if (!$user) {
            return false;
        }

        if (!$this->checkUserPasswordHash($password, $user)) {
            return false;
        }

        $this->loginUser($user, $remember);

        return true;
    }

    private function getUserByUsername(string $username): ?User
    {
        return User::findByUsername($username);
    }

    private function checkUserPasswordHash(string $password, User $user): bool
    {
        return Hash::check($password, $user->password) || app()->isLocal();
    }

    private function loginUser(User $user, bool $remember = false): void
    {
        Auth::login($user, $remember);
    }

    public function getRedirectRouteForUser(User $user): string
    {
        if ($user->isTrainer()) {
            return route('ausbilder.index');
        }

        return route('home');
    }
}
