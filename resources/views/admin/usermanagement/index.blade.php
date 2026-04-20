@use('App\DTO\SimpleUserViewData')
@use('Illuminate\Pagination\LengthAwarePaginator')
@php
    /** @var LengthAwarePaginator<int, SimpleUserViewData> $data */
@endphp
@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.benutzerverwaltung') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @can('usermanagement.store')
                        <a class="btn btn-primary" data-bs-target="#create-new-user" data-bs-toggle="modal">
                            <x-icon :classes="['me-2']" name="plus" />
                            {{ __('general.benutzer_anlegen') }}
                        </a>
                        @include('admin.usermanagement.modals.create')
                    @endcan
                </div>
            </div>
            @include('default.alerts')
        </div>
        <div class="row">
            <form action="?" class="space-y" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col">
                        <x-forms.input name="search">{{ __('general.suche') }}</x-forms.input>
                    </div>
                    <div class="col-md-3 col-12">
                        <x-forms.select :title="__('general.alle')" label="{{ __('general.fraktion') }}" multiple name="fraction">
                            @foreach ($fractions as $fraction)
                                <option @selected(in_array($fraction->getKey(), old('fraction', []))) value="{{ $fraction->getKey() }}">
                                    {{ $fraction->full_name }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-3 col-12">
                        <x-forms.input :default="$items_per_page" name="items_per_page" type="number">
                            {{ __('general.anzahl_pro_seite') }}
                        </x-forms.input>
                    </div>
                    <div class="col-md-3 col-12">
                        <x-forms.select label="{{ __('general.sortieren_nach') }}" name="sort_by">
                            <option @selected(old('sort_by', 'first_name') == 'first_name') value="first_name">{{ __('general.vorname') }}</option>
                            <option @selected(old('sort_by', 'first_name') == 'last_name') value="last_name">{{ __('general.nachname') }}</option>
                            <option @selected(old('sort_by', 'first_name') == 'created_at') value="created_at">{{ __('general.erstellt_am') }}</option>
                        </x-forms.select>
                    </div>
                    <div class="col-12 d-flex justify-content-end align-items-center gap-3" x-data="{ is_export: false, loading: false }">

                        <x-forms.checkbox :label-classes="['mb-0']" name="export" value="1" x-model="is_export">
                            {{ __('general.exportieren') }}
                        </x-forms.checkbox>

                        <div>
                            <x-forms.button x-bind:disabled="loading" x-on:click="if(is_export) { $event.target.form.submit(); loading = true; setTimeout(() => loading = false, 3000); }" x-text="is_export ? '{{ __('general.herunterladen') }}' : '{{ __('general.suchen') }}'">
                            </x-forms.button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-2 row">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.ausbildungen') }}</th>
                            <th>{{ __('general.ausbilder') }}</th>
                            <th>{{ __('general.ausbildungssperre') }}</th>
                            @canAny(['usermanagement.edit.personal_data', 'usermanagement.edit.account_data', 'usermanagement.edit.permissions', 'trainings.ban'])
                                <th></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            /** @var SimpleUserViewData $user */
                        @endphp
                        @forelse ($data ?? [] as $user)
                            <tr>
                                <td>
                                    <div class="py-1 d-flex align-items-center">
                                        <x-avatar :$user :initials="$user->initials" size="2"></x-avatar>
                                        <div class="flex-fill ms-2">
                                            <div class="font-weight-medium">
                                                <a class="cursor-pointer" data-bs-title="{{ $user->full_name }} ({{ $user->id }})" data-bs-toggle="tooltip" href="{{ route('profile.show', $user->id) }}">
                                                    {{ $user->salutation }} {{ $user->full_name }}
                                                </a>
                                                <div class="text-secondary">
                                                    {{ $user->fraction_data['default']['short_name'] }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="accordion-flush" id="qualifications-accordion-{{ $user->id }}">
                                        <div class="accordion-item">
                                            <div class="accordion-header">
                                                <button aria-controls="collapse-{{ $user->id }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-{{ $user->id }}" data-bs-toggle="collapse">
                                                    {{ __('general.qualifikationen_anzeigen') }}
                                                    <div class="accordion-button-toggle">
                                                        <x-icon name="chevron-down" />
                                                    </div>
                                                </button>

                                            </div>
                                            <div class="accordion-collapse collapse" data-bs-parent="#qualifications-accordion-{{ $user->id }}" id="collapse-{{ $user->id }}">
                                                <div class="accordion-body">
                                                    <ul style="list-style: none; padding-left: 0;">
                                                        @foreach ($qualifications as $qualification)
                                                            @php
                                                                $url = '';
                                                                $has_qualification = $user->qualifications->firstWhere('id', $qualification->getKey());
                                                            @endphp

                                                            <li>
                                                                @if (empty($has_qualification))
                                                                    <a>
                                                                        <span class="text-danger">
                                                                            <x-icon name="x" />
                                                                        </span>
                                                                    </a>
                                                                @else
                                                                    @php
                                                                        if (!empty($has_qualification->extra['training_id'])) {
                                                                            $url = route('ausbildung.show', $has_qualification->extra['training_id']);
                                                                        }
                                                                    @endphp
                                                                    <a @if (!empty($url)) href="{{ $url }}" @endif data-bs-title="{{ __('general.seit', ['date' => $has_qualification->description]) }}" data-bs-toggle="tooltip">
                                                                        <x-icon :classes="['text-success']" name="check" />
                                                                    </a>
                                                                @endif
                                                                {{ $qualification->name }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->is_trainer ? __('general.ja') : __('general.nein') }}
                                </td>
                                <td class="text-center">
                                    @if (!empty($user->training_ban_data))
                                        <a data-bs-target="#training-ban-reason-{{ $user->id }}" data-bs-toggle="modal">
                                            <x-icon :classes="['text-danger', 'cursor-pointer']" :hovertext="__('general.ausbildungssperre_bis', [
                                                'date_from' => $user->training_ban_data->extra['date_from'],
                                                'date_to' => $user->training_ban_data->extra['date_to'],
                                            ])" name="ban" />
                                        </a>
                                        @include('training.modals.ban_show', [
                                            'id' => $user->id,
                                            'full_name' => $user->full_name,
                                            'issuer_name' => $user->training_ban_data->label,
                                            'reason' => $user->training_ban_data->description,
                                        ])
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (!empty($user->qualifications->count()))
                                        @can('user.qualifications.remove')
                                            <a class="btn btn-sm btn-primary" data-bs-target="#remove-user-qualification-{{ $user->id }}" data-bs-toggle="modal">
                                                <x-icon :classes="['text-danger']" :hovertext="__('general.qualifikation_entfernen')" name="circle-minus" />
                                            </a>
                                            @include('training.modals.remove_qualification', [
                                                'user' => $user,
                                                'qualifications' => $user->qualifications,
                                            ])
                                        @endcan
                                    @endif
                                    @can('user.qualifications.assign')
                                        <a class="btn btn-sm btn-primary" data-bs-target="#add-user-qualification-{{ $user->id }}" data-bs-toggle="modal">
                                            <x-icon :classes="['text-success']" :hovertext="__('general.qualifikation_zuweisen')" name="plus" />
                                        </a>
                                        @include('training.modals.add_qualification', [
                                            'user' => $user,
                                        ])
                                    @endcan
                                    @can('trainingban.assign')
                                        @if (empty($user->training_ban_data))
                                            <a class="btn btn-sm btn-primary" data-bs-target="#add-training-ban-{{ $user->id }}" data-bs-toggle="modal">
                                                <x-icon :classes="['text-danger', 'cursor-pointer']" :hovertext="__('general.ausbildungssperre_verteilen')" name="ban" />
                                            </a>
                                            @include('training.modals.ban')
                                        @endif
                                    @endcan
                                    @canany(['usermanagement.edit.personal_data', 'usermanagement.edit.account_data', 'usermanagement.edit.permissions'])
                                        <a class="btn btn-sm btn-primary" href="{{ route('usermanagement.edit', $user->id) }}">
                                            <x-icon :classes="['text-white']" :hovertext="__('general.bearbeiten')" name="edit" />
                                        </a>
                                    @endcan
                                    @can('usermanagement.delete')
                                        <x-modal body_classes="text-center" form_action="{{ route('usermanagement.destroy', [$user->id]) }}" id="delete-user-{{ $user->id }}">

                                            <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
                                            <x-slot:body>
                                                <div class="text-center">
                                                    <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
                                                </div>
                                                <p class="text-warning">{!! __('general.benutzer_loeschen_confirm', [
                                                    'name' => $user->full_name,
                                                    'br' => '<br/>',
                                                ]) !!}
                                                </p>
                                            </x-slot:body>
                                            <x-slot:footer>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
                                                <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
                                            </x-slot:footer>
                                        </x-modal>
                                        <a class="btn btn-sm btn-danger" data-bs-target="#delete-user-{{ $user->id }}" data-bs-toggle="modal">
                                            <x-icon :classes="['text-white', 'cursor-pointer']" :hovertext="__('general.loeschen')" name="trash" />
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">
                                    <span style="fw-bold text-muted">{{ __('general.keine_benutzer_verfuegbar') }}</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-4">
                    <x-paginate :$data />
                </div>
            </div>
        </div>
    </div>
@endsection
