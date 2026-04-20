@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mb-2 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.rolle') }}
                </div>
                <h2 class="page-title">
                    {{ $role->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
        </div>
        @include('default.alerts')
        <x-card>
            <x-slot:header>
                <h2 class="card-title">{{ __('general.berechtigungen_aendern') }}</h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>{{ __('general.name') }}</th>
                                    <th>{{ __('general.zuweisung') }}</th>
                                </tr>
                            </thead>
                            @foreach ($permission_categories as $category)
                                <tr>
                                    <td class="text-center text-white bg-info" colspan="2">{{ $category->name }}</td>
                                </tr>
                                @foreach ($category->permissions as $permission)
                                    @php
                                        $has_permission_to = $role->hasPermissionTo($permission);
                                    @endphp
                                    <tr>
                                        <td>{{ $permission->translated_name }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <label class="form-check form-switch form-switch-3">
                                                    <input name="permissions[{{ $permission->id }}]" type="hidden" value="0">
                                                    <input @checked($has_permission_to) class="form-check-input" name="permissions[{{ $permission->id }}]" type="checkbox" value="1" value="{{ $permission->name }}">
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                    <x-forms.button :classes="['mt-2']">{{ __('general.speichern') }}</x-forms.button>
                </form>
            </x-slot:body>
        </x-card>

    </div>
@endsection
