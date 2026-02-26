<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\Fractions\Fraction;
use App\Models\User;
use App\Models\User\Fraction as UserFraction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class UserController extends Controller
{
    /**
     * Zeigt den Benutzer an
     *
     * @param  \App\Models\User                                                  $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        if (Auth::user()->getId() != $user->getId()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $genders = [
            [
                'name' => __('general.maennlich') . ' (' . __('general.anrede') . ': ' . __('general.herr') . ')',
                'value' => 'M',
            ],
            [
                'name' => __('general.weiblich') . ' (' . __('general.anrede') . ': ' . __('general.frau') . ')',
                'value' => 'W',
            ],
            [
                'name' => __('general.divers') . ' (' . __('general.anrede') . ': ' . __('general.ohne') . ')',
                'value' => 'D',
            ],
        ];
        $fractions = Fraction::get();

        return view('auth.settings', compact(
            'user',
            'genders',
            'fractions'
        ));
    }

    public function store(Request $request, User $user)
    {
        if (Auth::user()->getId() != $user->getId()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $user->load('account');
        $account = $user->account;
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date'],
            'birth_location' => ['required'],
            'gender' => ['required', 'in:M,W,D'],
            'default_fraction' => ['required', 'integer', 'exists:fractions,fraction_id'],
            'fraction.*' => ['integer', 'exists:fractions,fraction_id'],
            'password' => ['nullable', 'string', 'min:8'],
        ];

        $username = $request->input('username', $user->getName());
        $first_name = $request->input('first_name', $account->getFirstName());
        $last_name = $request->input('last_name', $account->getLastName());
        $date_of_birth = Carbon::create($request->input('date_of_birth', $account->getDateOfBirth()->format('d.m.Y')));
        $birth_location = $request->input('birth_location', $account->getBirthLocation());
        $default_fraction_id = $request->input('default_fraction', $account->getDefaultFractionId());
        $gender = $request->input('gender', $account->getGender());

        if ($username != $user->getName()) {
            $rules['username'] = ['required', 'string', 'max:255', 'unique:users,name'];
        }

        $request->validate($rules, $request->all());

        if (!empty($request->input('password'))) {
            $user->setPassword($request->input('password'));
        }
        $user->setName($username);
        $account->setFirstName($first_name);
        $account->setLastName($last_name);
        $account->setDateOfBirth($date_of_birth);
        $account->setBirthLocation($birth_location);
        $account->setGender($gender);

        $current_fractions = $account->fractions->pluck('fraction_id')->toArray();
        $remove_fractions = array_diff($current_fractions, [$default_fraction_id]);
        $fraction_model = UserFraction::firstOrCreate([
            'user_id' => $account->getId(),
            'fraction_id' => (int) $default_fraction_id,
        ]);
        $fraction_model->setDefault(1);
        $fraction_model->save();
        foreach ($remove_fractions as $fraction_id) {
            $model = UserFraction::where('user_id', $account->getId())
                ->where('fraction_id', $fraction_id)
                ->first();
            $model->delete();
        }

        if ($user->isDirty() || $user->account->isDirty()) {
            $user->save();
            $user->account->save();
            Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
        } else {
            Alert::addAlert(__('general.keine_aenderungen'));
        }

        return redirect()->back();
    }

    /**
     * Verlinkung von Discord Account
     *
     * @return void
     */
    public function linkDiscordAccount()
    {
        session(['discord_type' => 'LINK']);

        return Socialite::driver('discord')
            ->scopes(explode(',', config('services.discord.scopes')))
            ->redirect();
    }
}
