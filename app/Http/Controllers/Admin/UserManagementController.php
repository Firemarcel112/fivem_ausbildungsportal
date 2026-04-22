<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Export\UserWithQualificationsDTO;
use App\DTO\SimpleUserViewData;
use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Models\Fraction;
use App\Models\PermissionCategorie;
use App\Models\Qualification;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use App\Services\Export\UserWithQualificationsService;
use Arr;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    use AuthorizesRequests;

    public UserWithQualificationsService $export_service;

    public function __construct(
        UserWithQualificationsService $export_service
    ) {
        $this->export_service = $export_service;
    }

    /**
     * Userausgabe
     *
     * @param  Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

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
        $qualifications = Qualification::getAllQualifications(true);

        $fractions = Fraction::get();

        $accounts_table_name = (new Account)->getTable();

        $items_per_page = $request->integer('items_per_page', 10);

        $data = User::with([
            'discord',
            'account.fractions',
            'account.qualifications',
            'activeTrainingBan.issuer.account',
            'permissions',
            'roles.permissions',
        ])
            ->joinAccounts()
            ->when(!empty($search), function ($query) use ($search) {
                $query->searchAccount($search);
            })
            ->when(!empty($fraction), function ($query) use ($fraction) {
                $query->whereHas('account.fractions', function ($query) use ($fraction) {
                    $query->whereIn('fractions.fraction_id', $fraction);
                });
            })
            ->when(!empty($sort_by), function (Builder $query) use ($sort_by, $accounts_table_name) {
                switch ($sort_by) {
                    case 'last_name':
                        return $query->orderBy($accounts_table_name . '.last_name');
                    case 'created_at':
                        return $query->orderBy($accounts_table_name . '.created_at');
                    default:
                        return $query->orderBy($accounts_table_name . '.first_name');
                }
            });

        if ($request->has('export')) {
            $data = $data->get()
                ->map(fn(User $user) => SimpleUserViewData::fromModel($user));
            $this->export($request, $data, $qualifications->pluck('name')->toArray());

            return;
        }

        $paginator = $data->paginate($items_per_page);

        $paginator->getCollection()->transform(fn(User $user) => SimpleUserViewData::fromModel($user));

        return view(
            'admin.usermanagement.index',
            [
                'data' => $paginator,
                'fractions' => $fractions,
                'qualifications' => $qualifications,
                'items_per_page' => $items_per_page,
            ],
        );
    }

    /**
     * Benutzer bearbeiten
     *
     * @param  \App\Models\User                                                  $user
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

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
        $current_user_permissions = Auth::user()?->getAllPermissions();
        $can_give_permission = [];

        foreach ($permission_names as $name) {
            $has_direct_permission_to[$name] = $user->hasDirectPermission($name);
            $has_permission_to[$name] = $user->hasPermissionTo($name, null);
            $permission_from_role_id[$name] = $role_permissions->where('name', $name)->first()->pivot->role_id ?? null;
            $can_give_permission[$name] = $current_user_permissions->firstWhere('name', $name)->exists ?? Auth::user()?->isSuperadmin() ?? false;
        }
        $account = $user->account;

        $user_fractions = $account?->fractions
            ->where('pivot.default', 0)
            ->pluck('fraction_id')
            ->toArray();

        return view('admin.usermanagement.edit', compact(
            'user',
            'fractions',
            'permission_categories',
            'role_permissions',
            'roles',
            'has_permission_to',
            'has_direct_permission_to',
            'permission_from_role_id',
            'can_give_permission',
            'account',
            'user_fractions',
        ));
    }

    /**
     * Aktualisiert einen Benutzer
     *
     * @param  Request                           $request
     * @param  \App\Models\User                  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $permission_account_data = Auth::user()?->can('usermanagement.edit.account_data');
        $permission_personal_data = Auth::user()?->can('usermanagement.edit.personal_data');
        $permission_permissions = Auth::user()?->can('usermanagement.edit.permissions');

        if (!Auth::user()?->canAny([
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
     * @param  \App\Http\Requests\UserStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreRequest $request)
    {
        $this->authorize('create', User::class);
        $new_password = Str::random(12);

        DB::beginTransaction();
        $user = new User;
        $user->name = $request->input('username');
        $user->password = $new_password;
        $user->email = substr(strtolower($request->input('first_name')), 0, 1) . '.' . trim(strtolower(str_replace(' ', '_', $request->last_name))) . '@example.com';
        $user->save();

        $account = new Account;
        $account->user_id = $user->getQueueableId();
        $account->first_name = $request->input('first_name');
        $account->last_name = $request->input('last_name');
        $account->date_of_birth = Carbon::create($request->input('date_of_birth'));
        $account->save();

        $fraction_model = new UserFraction;
        $fraction_model->user_id = $user->getQueueableId();
        $fraction_model->fraction_id = $request->input('default_fraction');
        $fraction_model->default = 1;
        $fraction_model->save();

        DB::commit();
        Alert::addAlert(__('general.neues_passwort', ['password' => $new_password]), 'warning');
        Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');

        return redirect()->back();
    }

    /**
     * Aktualisiert die Accountdaten
     *
     * @param  Request          $request
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function updateAccountData(Request $request, User $user): mixed
    {
        $user_data = [
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'default_fraction' => $request->input('default_fraction', null),
            'fractions' => Arr::flatten($request->input('fraction_ids', [])),
            'date_of_birth' => Carbon::create($request->input('date_of_birth')),
            'birth_location' => $request->input('birth_location'),
            'gender' => $request->input('gender'),
        ];
        $user_data['fractions'][] = $request->input('default_fraction', null);
        /**
         * @var Account $account
         */
        $account = $user->account;

        $account->first_name = $user_data['first_name'];
        $account->date_of_birth = $user_data['date_of_birth'];
        $account->last_name = $user_data['last_name'];
        $account->birth_location = $user_data['birth_location'];
        $account->gender = $user_data['gender'];

        $current_fractions = $account->fractions->pluck('fraction_id')->toArray();
        $remove_fractions = array_diff($current_fractions, $user_data['fractions']);
        foreach ($user_data['fractions'] as $fraction_id) {
            $fraction_model = UserFraction::firstOrCreate([
                'user_id' => $account->getKey(),
                'fraction_id' => (int) $fraction_id,
            ]);
            $fraction_model->default = $fraction_id == $user_data['default_fraction'] ? 1 : 0;
            $fraction_model->save();
        }
        foreach ($remove_fractions as $fraction_id) {
            $model = UserFraction::where('user_id', $account->getKey())
                ->where('fraction_id', $fraction_id)
                ->first();
            $model->delete();
        }
        if ($account->isDirty()) {
            if ($account->save()) {
                if ($user->getKey() == Auth::user()?->getKey()) {
                    session()->put('name', $account->full_name);
                }
                Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');

                return redirect()->back();
            }
        }

        return redirect()->back();
    }

    /**
     * Aktualisiert die Userdaten
     *
     * @param  Request          $request
     * @param  \App\Models\User $user
     * @return mixed
     */
    public function updateUserData(Request $request, User $user)
    {
        $account_data = [
            'username' => $request->input('username'),
            'change_password' => $request->has('change_password'),
        ];
        $user->name = $account_data['username'];
        if ($account_data['change_password']) {
            $new_password = Str::random(12);
            $user->password = $new_password;
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
     *
     * @param  Request          $request
     * @param  \App\Models\User $user
     * @return void
     */
    public function updatePermissions(Request $request, User $user): void
    {
        $get_current_user_permissions = Auth::user()?->getAllPermissions();
        $get_current_user_roles = Auth::user()->roles;
        if ($request->has('permissions')) {
            $grant_permission_id = [];
            $revoke_permission_id = [];
            foreach ($request->permissions as $key => $value) {
                $current_user_has_permission = $get_current_user_permissions->firstWhere('id', $key)->exists ?? false;
                if (!$current_user_has_permission && !Auth::user()?->isSuperadmin()) {
                    continue;
                }
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
                $current_user_has_role = $get_current_user_roles->firstWhere('id', $key)->exists ?? false;
                if (!$current_user_has_role && !Auth::user()?->isSuperadmin()) {
                    continue;
                }
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
     * @param  \App\Models\User                  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->checkPermission('usermanagement.delete');
        if ($user->isSuperadmin() || Auth::user()?->getKey() == $user->getKey()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $user->account->directQualifications->each(function ($qualification) {
            $qualification->delete();
        });
        $user->account->trainings->each(function ($participant) {
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

    /**
     * Exportiert die Benutzer mit ihren Qualifikationen
     *
     * @param Request $request
     * @param Collection<int, SimpleUserViewData>
     * @return void
     */
    public function export(Request $request, ?Collection $data = null, $qualifications = null)
    {
        $data = $data->map(function (SimpleUserViewData $user) use ($qualifications) {
            return UserWithQualificationsDTO::fromSimpleUserViewDataModel($user, $qualifications);
        });

        $this->export_service->export(
            $data,
            'users-with-qualifications-' . now()->format('Y-m-d')
        );
    }
}
