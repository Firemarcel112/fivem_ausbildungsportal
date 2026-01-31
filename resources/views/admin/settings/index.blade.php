@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.einstellungen') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
            @include('default.alerts')
        </div>
        <div class="mt-2 row">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.wert') }}</th>
                            <th>{{ __('general.beschreibung') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($settings as $setting)
                            <tr>
                                <td>{{ $setting->getKey() }}</td>
                                <td>
                                    @if (empty($setting->getValue()))
                                        <span class="text-muted">{{ __('general.kein_wert') }}</span>
                                    @else
                                        {{ $setting->getValue() }}
                                    @endif
                                </td>
                                <td>
                                    {{ $setting->getDescription() }}
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-primary" data-bs-target="#edit-settings-{{ $setting->getKey() }}" data-bs-toggle="modal">
                                        <x-icon :classes="['text-white']" :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                </td>
                            </tr>
                            @include('admin.settings.modals.edit', [
                                'id' => "edit-settings-{$setting->getKey()}",
                                'setting' => $setting,
                            ])
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
