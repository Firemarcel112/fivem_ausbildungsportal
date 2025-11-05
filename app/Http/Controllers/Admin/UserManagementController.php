<?php

namespace App\Http\Controllers\Admin;

use Arr;
use DB;
use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\Fractions\Fraction;
use App\Models\PermissionCategorie;
use App\Models\Qualifications\Qualification;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{

    /**
     * Userausgabe
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        if ($request->isMethod('POST')) {
            $request->flash();
        }
        $search = $request->search ?? '';
        $fraction = $request->fraction ?? [];
        if (!is_array($fraction)) {
            $fraction = array_map('intval', explode(',', $fraction));
        } else {
            $fraction = array_map('intval', $fraction);
        }
        $sort_by = $request->sort_by ?? 'first_name';
        $qualifications = Cache::rememberForever('qualifications_all', function () {
            return Qualification::isOrderByDefault()
                ->get();
        });
        $fractions = Fraction::get();

        $data = User::with([
            'discord',
            'account.fractions',
            'account.qualifications',
            'activeTrainingBan.issuer.account'
        ])
            ->when(!empty($search), function ($query) use ($search) {
                $query->whereHas('account', function ($query) use ($search) {
                    $query->isSearch($search);
                });
            })
            ->when(!empty($fraction), function ($query) use ($fraction) {
                $query->whereHas('account.fractions', function ($query) use ($fraction) {
                    $query->whereIn('fractions.fraction_id', $fraction);
                });
            })
            ->get();

        switch ($sort_by) {
            case 'last_name':
                $data = $data->sortBy(fn($user) => $user->account->getLastName());
                break;
            case 'created_at':
                $data = $data->sortByDesc('created_at');
                break;
            default:
                $data = $data->sortBy(fn($user) => $user->account->getFirstName());
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
        $items_per_page = $request->items_per_page ?? 10;
        $data = new LengthAwarePaginator(
            $data->forPage($request->input('page', 1), $items_per_page),
            $data->count(),
            $items_per_page,
            $request->input('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view(
            'admin.usermanagement.index',
            compact(
                'data',
                'fractions',
                'qualifications',
                'items_per_page',
                'genders',
            )
        );
    }

    /**
     * Benutzer bearbeiten
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(User $user)
    {
        if ($user->isSuperadmin() && !Auth::user()->isSuperadmin()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        if (!Auth::user()->canAny([
            'usermanagement.edit.account_data',
            'usermanagement.edit.personal_data',
            'usermanagement.edit.permissions',
        ])) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->route('usermanagement.index');
        }
        $user->load('account.fractions');
        $fractions = Fraction::get();
        $permission_categories = PermissionCategorie::with('permissions')
            ->orderByDefault()
            ->get();

        $permission_names = $permission_categories
            ->pluck('permissions.*.name')
            ->flatten()
            ->toArray();

        $has_direct_permission_to = [];
        $has_permission_to = [];
        $permission_from_role_id = [];
        $role_permissions = $user->getPermissionsViaRoles();
        $roles = Role::with(['permissions' => function ($query) {
            $query->orderByRaw("CASE WHEN name LIKE '%*%' THEN 0 ELSE 1 END, name");
        }])
            ->get();
        foreach ($permission_names as $name) {
            $has_direct_permission_to[$name] = $user->hasDirectPermission($name);
            $has_permission_to[$name] = $user->hasPermissionTo($name, null, false);
            $permission_from_role_id[$name] = $role_permissions?->where('name', $name)?->first()->pivot?->role_id ?? null;
        }
        $account = $user->account;

        $user_fractions = $account->fractions
            ->where('pivot.default', 0)
            ->pluck('fraction_id')
            ->toArray();

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

        return view('admin.usermanagement.edit', compact(
            'user',
            'fractions',
            'permission_categories',
            'role_permissions',
            'roles',
            'has_permission_to',
            'has_direct_permission_to',
            'permission_from_role_id',
            'account',
            'user_fractions',
            'genders',
        ));
    }

    /**
     * Aktualisiert einen Benutzer
     *
     * @param Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $permission_account_data = Auth::user()->can('usermanagement.edit.account_data');
        $permission_personal_data = Auth::user()->can('usermanagement.edit.personal_data');
        $permission_permissions = Auth::user()->can('usermanagement.edit.permissions');

        if (!Auth::user()->canAny([
            'usermanagement.edit.account_data',
            'usermanagement.edit.personal_data',
            'usermanagement.edit.permissions',
        ])) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }

        if (!empty($request->has('username')) && $permission_account_data) {
            $this->updateUserData($request, $user);
        }

        if (!empty($request->has('first_name')) && $permission_personal_data) {
            $this->updateAccountData($request, $user);
        }
        if ($permission_permissions) {
            $this->updatePermissions($request, $user);
        }

        return redirect()->back();
    }

    /**
     * Neuen Benutzer anlegen
     *
     * @param \App\Http\Requests\UserStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreRequest $request)
    {
        if (!Auth::user()->can('usermanagement.store')) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        $new_password = Str::random(12);

        DB::beginTransaction();
        $user = new User();
        $user->setName($request->input('username'));
        $user->setPassword($new_password);
        $user->setEmail(substr(strtolower($request->input('first_name')), 0, 1) . '.' . trim(strtolower(str_replace(' ', '_', $request->last_name))) . '@example.com');
        $user->save();

        $account = new Account();
        $account->setUserId($user->getQueueableId());
        $account->setFirstName($request->input('first_name'));
        $account->setLastName($request->input('last_name'));
        $account->setDateOfBirth(Carbon::create($request->input('date_of_birth')));
        $account->save();

        $fraction_model = new UserFraction();
        $fraction_model->setUserId($user->getQueueableId());
        $fraction_model->setFractionId($request->input('default_fraction'));
        $fraction_model->setDefault(1);
        $fraction_model->save();

        DB::commit();
        Alert::addAlert(__('general.neues_passwort', ['password' => $new_password]), 'warning');
        Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
        return redirect()->back();
    }

    /**
     * Aktualisiert die Accountdaten
     * @param Request $request
     * @param \App\Models\User $user
     * @return mixed
     */
    public function updateAccountData(Request $request, User $user)
    {
        $user_data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'default_fraction' => $request->input('default_fraction', null),
            'fractions' => Arr::flatten($request->input('fraction_ids', [])),
            'date_of_birth' => Carbon::create($request->input('date_of_birth')),
            'birth_location' => $request->input('birth_location'),
            'gender' => $request->input('gender')
        ];
        $user_data['fractions'][] = $request->input('default_fraction', null);
        $account = $user->account;
        $account->setFirstName($user_data['first_name']);
        $account->setDateOfBirth($user_data['date_of_birth']);
        $account->setLastName($user_data['last_name']);
        $account->setBirthLocation($user_data['birth_location']);
        $account->setGender($user_data['gender']);

        $current_fractions = $account->fractions->pluck('fraction_id')->toArray();
        $remove_fractions = array_diff($current_fractions, $user_data['fractions']);
        foreach ($user_data['fractions'] as $fraction_id) {
            $fraction_model = UserFraction::firstOrCreate([
                'user_id' => $account->getId(),
                'fraction_id' => (int)$fraction_id,
            ]);
            $fraction_model->setDefault($fraction_id == $user_data['default_fraction'] ? 1 : 0);
            $fraction_model->save();
        }
        foreach ($remove_fractions as $fraction_id) {
            $model = UserFraction::where('user_id', $account->getId())
                ->where('fraction_id', $fraction_id)
                ->first();
            $model->delete();
        }
        if ($account->isDirty()) {
            if ($account->save()) {
                if ($user->getId() == Auth::user()->getId()) {
                    session()->put('name', $account->getFullName());
                }
                Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
                return redirect()->back();
            }
        }
    }

    /**
     * Aktualisiert die Userdaten
     * @param Request $request
     * @param \App\Models\User $user
     * @return mixed
     */
    public function updateUserData(Request $request, User $user)
    {
        $account_data = [
            'username' => $request->input('username'),
            'change_password' => $request->has('change_password'),
        ];
        $user->setName($account_data['username']);
        if ($account_data['change_password']) {
            $new_password = Str::random(12);
            $user->setPassword($new_password);
            Alert::addAlert(__('general.neues_passwort', ['password' => $new_password]), 'warning');
        }

        if ($user->isDirty()) {
            if ($user->save()) {

                Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
                return redirect()->back();
            }
        }
    }

    /**
     * Aktualisiert die Permission und Roles
     * @param Request $request
     * @param \App\Models\User $user
     * @return mixed
     */
    public function updatePermissions(Request $request, User $user)
    {
        if ($request->has('permissions')) {
            $grant_permission_id = [];
            $revoke_permission_id = [];
            foreach ($request->permissions as $key => $value) {
                if ($value == 1) {
                    $grant_permission_id[] = $key;
                } else {
                    $revoke_permission_id[] = $key;
                }
            }
            $user->syncPermissions($grant_permission_id, $revoke_permission_id);
        }
        if ($request->has('roles')) {
            $grant_role_id = [];
            $revoke_role_id = [];
            foreach ($request->roles as $key => $value) {
                if ($value == 1) {
                    $grant_role_id[] = $key;
                } else {
                    $revoke_role_id[] = $key;
                }
            }
            $user->syncRoles($grant_role_id, $revoke_role_id);
        }
    }

    /**
     * Löscht einen Benutzer
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->checkPermission('usermanagement.delete');
        if ($user->isSuperadmin() || Auth::user()->getId() == $user->getId()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        $user->account->directQualifications->each(function ($qualification) use ($user) {
            $qualification->delete();
        });
        $user->account->trainings->each(function ($participant) use ($user) {
            $participant->delete();
        });

        $user->permissions->each(function ($permission) use ($user) {
            $user->revokePermissionTo($permission->name);
        });
        $user->account->delete();
        $user->delete();

        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');
        return redirect()->back();
    }
}
