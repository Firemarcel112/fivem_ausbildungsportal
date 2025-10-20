@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class=" mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.benutzerverwaltung') }}
                </div>
                <h2 class="page-title">
                    {{ $user->getFullName() }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
            @include('default.alerts')
        </div>
        @can('usermanagement.edit.personal_data')
            <div class="accordion accordion-flush bg-surface rounded">
                <div class="accordion-header">
                    <button aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-user-data" data-bs-toggle="collapse" type="button">
                        {{ __('general.daten_aendern') }}
                        <div class="accordion-button-toggle">
                            <x-icon name="chevron-down" />
                        </div>
                    </button>
                </div>
                <div class="accordion-collapse collapse p-4" id="collapse-user-data">
                    <form action="{{ route('usermanagement.update', $user) }}" class="space-y" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <x-forms.input :default="$account->getFirstName()" name="first_name" required>
                                    {{ __('general.vorname') }}
                                </x-forms.input>
                            </div>
                            <div class="col-6">
                                <x-forms.input :default="$account->getLastName()" name="last_name" required>
                                    {{ __('general.nachname') }}
                                </x-forms.input>
                            </div>
                        </div>
                        <x-forms.select :label="__('general.default_fraktion')" name="default_fraction" required>
                            @foreach ($fractions as $fraction)
                                <option @selected($fraction->getId() == $account->getDefaultFractionId()) value="{{ $fraction->getId() }}">
                                    {{ $fraction->getFullName() }}
                                </option>
                            @endforeach
                        </x-forms.select>
                        <x-forms.select :label="__('general.fraktion')" multiple name="fraction_ids[]">
                            @foreach ($fractions as $fraction)
                                @continue($fraction->getId() == $account->getDefaultFractionId())
                                <option @selected(in_array($fraction->getId(), $user_fractions)) value="{{ $fraction->getId() }}">
                                    {{ $fraction->getFullName() }}
                                </option>
                            @endforeach
                        </x-forms.select>

                        <div class="row">
                            <div class="col text-end">
                                <x-forms.button>
                                    {{ __('general.speichern') }}
                                </x-forms.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('usermanagement.edit.account_data')
            <div class="accordion accordion-flush bg-surface rounded">
                <div class="accordion-header">
                    <button aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-account-data" data-bs-toggle="collapse" type="button">
                        {{ __('general.accountdaten_aendern') }}
                        <div class="accordion-button-toggle">
                            <x-icon name="chevron-down" />
                        </div>
                    </button>
                </div>
                <div class="accordion-collapse collapse p-4" id="collapse-account-data">
                    <form action="{{ route('usermanagement.update', $user) }}" class="space-y" method="POST">
                        @csrf
                        <x-forms.input :default="$user->getName()" name="username" required>
                            {{ __('general.benutzername') }}
                        </x-forms.input>

                        <x-forms.checkbox name="change_password">
                            {{ __('general.passwort_aendern_aktivieren') }}
                        </x-forms.checkbox>

                        <div class="row">
                            <div class="col text-end">
                                <x-forms.button>
                                    {{ __('general.speichern') }}
                                </x-forms.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('usermanagement.edit.permissions')
            <div class="accordion accordion-flush bg-surface rounded">
                <div class="accordion-header">
                    <button aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-permissions" data-bs-toggle="collapse" type="button">
                        {{ __('general.berechtigungen_aendern') }}
                        <div class="accordion-button-toggle">
                            <x-icon name="chevron-down" />
                        </div>
                    </button>
                </div>
                <div class="accordion-collapse collapse p-4" id="collapse-permissions">
                    <form action="{{ route('usermanagement.update', $user) }}" class="space-y" method="POST">
                        @csrf
                        <table class="table table-responsive">
                            <thead>
                                <tr>
                                    <th>{{ __('general.name') }}</th>
                                    <th>{{ __('general.zuweisung') }}</th>
                                </tr>
                            </thead>
                            @if ($roles->isNotEmpty())
                                <tr>
                                    <td class="text-center text-white bg-secondary" colspan="2">{{ __('general.rollen') }}</td>
                                </tr>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td>{{ $role->name }}
                                            @if (!empty($role->permissions->first()))
                                                <a aria-controls="collapse-role-permissions-{{ $role->id }}" aria-expanded="false" class="text-decoration-none" data-bs-toggle="collapse" href="#collapse-role-permissions-{{ $role->id }}" role="button">
                                                    {{ __('general.zeige_berechtigungen') }}
                                                </a>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input name="roles[{{ $role->id }}]" type="hidden" value="0">
                                                <input @checked($user->hasRole($role->name, null, false)) class="form-check-input" name="roles[{{ $role->id }}]" type="checkbox" value="1">
                                            </div>
                                        </td>
                                    </tr>
                                    @if (!empty($role->permissions->first()))
                                        <tr class="collapse" id="collapse-role-permissions-{{ $role->id }}">
                                            <td colspan="2">
                                                <ul>
                                                    @foreach ($role->permissions as $rolePermission)
                                                        <li>{{ $rolePermission->getTranslatedName() }}</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            @if ($permission_categories->isNotEmpty())
                                <tr>
                                    <td class="text-center text-white bg-secondary" colspan="2">{{ __('general.berechtigungen') }}</td>
                                </tr>
                                @foreach ($permission_categories as $category)
                                    <tr>
                                        <td class="text-center text-white bg-info" colspan="2">{{ $category->getName() }}</td>
                                    </tr>
                                    @foreach ($category->permissions as $permission)
                                        <tr>
                                            <td>{{ $permission->getTranslatedName() }}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <label class="form-check form-switch form-switch-3">
                                                        <input name="permissions[{{ $permission->id }}]" type="hidden" value="0">
                                                        <input @checked($has_permission_to[$permission->name]) class="form-check-input" name="permissions[{{ $permission->id }}]" type="checkbox" value="1" value="{{ $permission->getTranslatedName() }}">
                                                    </label>
                                                    @if (!$has_direct_permission_to[$permission->name] && $has_permission_to[$permission->name] && empty($permission_from_role_id[$permission->name]))
                                                        <x-icon :hovertext="__('general.recht_geerbt')" name="info-circle" />
                                                    @endif
                                                    @if (!empty($permission_from_role_id[$permission->name]))
                                                        <x-icon :hovertext="__('general.recht_von_rolle_geerbt')" name="info-circle" />
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            @endif
                        </table>
                        <div class="row">
                            <div class="col text-end">
                                <x-forms.button>
                                    {{ __('general.speichern') }}
                                </x-forms.button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

    </div>
@endsection
