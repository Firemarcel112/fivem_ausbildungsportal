<?php

namespace App\Http\Controllers\Auth;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\Fractions\Fraction;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use Carbon\Carbon;
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
     * @param \App\Http\Requests\UserStoreRequest $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreRequest $request)
    {
        $fractions = array_merge($request->input('fraction', []), [$request->input('default_fraction')]);
        $fractions = array_unique($fractions);
        DB::beginTransaction();
        $user = new User();
        $user->setName($request->input('username'));
        $user->setPassword($request->input('password'));
        if ($user->save()) {
            $alert_success = true;
        };

        $account = new Account();
        $account->setUserId($user->getQueueableId());
        $account->setFirstName($request->input('first_name'));
        $account->setLastName($request->input('last_name'));
        $account->setGender($request->input('gender'));
        $account->setBirthLocation($request->input('birth_location'));
        $account->setDateOfBirth(Carbon::create($request->input('date_of_birth')));
        $account->save();

        foreach ($fractions as $fraction_id) {
            $fraction_model = new UserFraction();
            $fraction_model->setUserId($account->getQueueableId());
            $fraction_model->setFractionId($fraction_id);
            $fraction_model->setDefault($fraction_id == $request->input('default_fraction') ? 1 : 0);
            $fraction_model->save();
        }

        if ($account->save() && $alert_success) {
            Alert::addAlert(__('general.registrierung_erfolgreich'));
        } else {
            DB::rollBack();
            Alert::addAlert(__('general.registrierung_fehlgeschlagen'), 'error');
        }
        DB::commit();

        return redirect()->route('login.index');
    }
}
