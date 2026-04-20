<?php

namespace App\Http\Controllers\Auth;

use App\Actions\UpdateUserAction;
use App\DTO\SimpleListItem;
use App\DTO\SimpleUserViewData;
use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Fraction;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Zeigt den Benutzer an
     *
     * @param  \App\Models\User                                                  $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {

        $this->authorize('updateSettings', $user);

        $user = SimpleUserViewData::fromModel($user);

        $fractions = Fraction::get()
            ->map(fn(Fraction $item) => new SimpleListItem(
                $item->getKey(),
                $item->full_name
            ));

        return view('auth.settings', compact(
            'user',
            'fractions'
        ));
    }

    /**
     * Aktualisiert den Benutzer
     *
     * @param  UpdateUserRequest                 $request
     * @param  User                              $user
     * @param  UpdateUserAction                  $update_user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $update_user)
    {
        $this->authorize('updateSettings', $user);

        $action = $update_user->execute($user, $request->safe()->toArray());

        Alert::addAlert($action->message, $action->success ? 'success' : 'danger');

        return redirect()->back();
    }
}
