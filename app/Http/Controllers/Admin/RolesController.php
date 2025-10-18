<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role as EnumsRole;
use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Permission;

class RolesController extends Controller
{
    /**
     * Anzeige der Rollen
     *
     * @return mixed
     */
    public function index()
    {
        if (!Auth::user()->isSuperadmin()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        $roles = Role::with(['permissions', 'users'])
            ->get();
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Bearbeitenseite der Rollen
     *
     * @return mixed
     */
    public function edit(Role $role)
    {
        if (!Auth::user()->isSuperadmin()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }

        $permissions = Permission::get();
        $permission_names = $role->permissions
            ->pluck('name')
            ->toArray();
        return view('admin.roles.edit', compact('role', 'permissions', 'permission_names'));
    }

    /**
     * Bearbeiten der Rollen
     *
     * @return mixed
     */
    public function update(Request $request, Role $role)
    {
        if (!Auth::user()->isSuperadmin()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }
        foreach ($request->permissions as $key => $value) {
            if ($value) {
                $role->givePermissionTo($key);
            } else {
                $role->revokePermissionTo($key);
            }
        }
        Alert::addAlert(__('general.berechtigungen_geaendert'), 'success');
        return redirect()->back();
    }

    public function store(Request $request)
    {
        if (!Auth::user()->isSuperadmin()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');
            return redirect()->back();
        }

        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        Alert::addAlert(__('general.rolle_erstellt'), 'success');
        return redirect()->back();
    }
}
