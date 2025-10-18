@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mb-2 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.rollen') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary" data-bs-target="#create-new-role" data-bs-toggle="modal">
                        <x-icon :classes="['me-2']" name="plus" />
                        {{ __('general.rolle_anlegen') }}
                    </a>
                    @include('admin.roles.modals.create')
                </div>
            </div>
        </div>
        <div class="mt-2 row">
            @include('default.alerts')

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.berechtigungen') }}</th>
                            <th>{{ __('general.zugewiesene_personen') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $role->getName() }}</td>
                                <td>
                                    <ul>
                                        @foreach ($role->permissions as $permission)
                                            <li>{{ $permission->getTranslatedName() }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <ul>
                                        @foreach ($role->users as $user)
                                            <li>
                                                <a href="{{ route('usermanagement.edit', $user) }}">
                                                    {{ $user->getFullName() }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-primary" href="{{ route('admin.roles.edit', $role) }}">
                                        <x-icon :classes="['text-white']" :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
