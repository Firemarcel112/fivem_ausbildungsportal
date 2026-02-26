<?php

namespace App\Http\Controllers\Admin;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\PermissionCategorie;
use App\Models\Role;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    /**
     * Anzeige der Rollen
     *
     * @return mixed
     */
    public function index()
    {
        $this->checkPermission('administration.roles.edit');

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
        $this->checkPermission('administration.roles.edit');

        $permission_categories = PermissionCategorie::with('permissions')
            ->orderByDefault()
            ->get();

        $permission_names = $role->permissions
            ->pluck('name')
            ->toArray();

        return view('admin.roles.edit', compact(
            'role',
            'permission_categories',
            'permission_names'
        ));
    }

    /**
     * Bearbeiten der Rollen
     *
     * @return mixed
     */
    public function update(Request $request, Role $role)
    {
        $this->checkPermission('administration.roles.edit');

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

    /**
     * Rolle Speichern
     *
     * @param  \Illuminate\Http\Request          $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->checkPermission('administration.roles.edit');

        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->name]);

        Alert::addAlert(__('general.rolle_erstellt'), 'success');

        return redirect()->back();
    }
}
