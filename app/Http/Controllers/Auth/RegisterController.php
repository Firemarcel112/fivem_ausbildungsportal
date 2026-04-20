<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\Fraction;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function index()
    {
        $fractions = Fraction::get();
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

        return view('auth.register', compact('fractions', 'genders'));
    }

    /**
     * Speichert einen Benutzer
     *
     * @param  \App\Http\Requests\UserStoreRequest     $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreRequest $request)
    {
        $alert_success = false;
        $fractions = array_merge($request->input('fraction', []), [$request->input('default_fraction')]);
        $fractions = array_unique($fractions);
        DB::beginTransaction();
        $user = new User;
        $user->name = $request->input('username');
        $user->password = $request->input('password');
        if ($user->save()) {
            $alert_success = true;
        }

        $account = new Account;
        $account->user_id = $user->getQueueableId();
        $account->first_name = $request->input('first_name');
        $account->last_name = $request->input('last_name');
        $account->gender = $request->input('gender');
        $account->birth_location = $request->input('birth_location');
        $account->date_of_birth = $request->input('date_of_birth');
        $account->save();

        foreach ($fractions as $fraction_id) {
            $fraction_model = new UserFraction;
            $fraction_model->user_id = $account->getQueueableId();
            $fraction_model->fraction_id = $fraction_id;
            $fraction_model->default = $fraction_id == $request->input('default_fraction') ? 1 : 0;
            $fraction_model->save();
        }

        if ($alert_success) {
            Alert::addAlert(__('general.registrierung_erfolgreich'));
        } else {
            DB::rollBack();
            Alert::addAlert(__('general.registrierung_fehlgeschlagen'), 'error');
        }
        DB::commit();

        return redirect()->route('login.index');
    }
}
