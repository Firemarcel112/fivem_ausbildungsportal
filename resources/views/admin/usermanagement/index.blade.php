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
                    <div class="col-12 text-end">
                        <x-forms.button>{{ __('general.suchen') }}</x-forms.button>
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
                            <th>{{ __('general.fraktion') }}</th>
                            <th>{{ __('general.ausbildungen') }}</th>
                            <th>{{ __('general.ausbilder') }}</th>
                            <th>{{ __('general.ausbildungssperre') }}</th>
                            @canAny(['usermanagement.edit.personal_data', 'usermanagement.edit.account_data', 'usermanagement.edit.permissions', 'trainings.ban'])
                                <th></th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data ?? [] as $user)
                            <tr>
                                <td>
                                    <div class="py-1 d-flex align-items-center">
                                        <x-avatar :$user size="2"></x-avatar>
                                        <div class="flex-fill ms-2">
                                            <div class="font-weight-medium">
                                                <a class="cursor-pointer" data-bs-title="{{ $user->getDiscordName() }} ({{ $user->getId() }})" data-bs-toggle="tooltip" href="{{ route('profile.show', $user) }}">
                                                    {{ $user->getSalutation() }} {{ $user->getFullName() }}
                                                </a>
                                                @if ($user->discord && !empty(config('services.discord.client_id')))
                                                    <x-icon :hovertext="__('general.discord_account_verknuepft')" name="discord" />
                                                @endif
                                                <div class="text-secondary">
                                                    {{ $user->account->fractions->firstWhere('pivot.default', 1)->full_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="accordion-flush" id="fractions-accordion-{{ $user->getId() }}">
                                        <div class="accordion-item">
                                            <div class="accordion-header">
                                                <button aria-controls="collapse-fractions-{{ $user->getId() }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-fractions--{{ $user->getId() }}" data-bs-toggle="collapse">
                                                    {{ __('general.fraktionen_anzeigen') }}
                                                    <div class="accordion-button-toggle">
                                                        <x-icon name="chevron-down" />
                                                    </div>
                                                </button>

                                            </div>
                                            <div class="accordion-collapse collapse" data-bs-parent="#fractions-accordion-{{ $user->getId() }}" id="collapse-fractions--{{ $user->getId() }}">
                                                <div class="accordion-body">
                                                    <ul style="list-style: none; padding-left: 0;">
                                                        @foreach ($user->account->fractions as $fraction)
                                                            <li>
                                                                {{ $fraction->full_name }}
                                                                @if ($fraction->default)
                                                                    {{ __('general.standard') }}
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="accordion-flush" id="qualifications-accordion-{{ $user->getId() }}">
                                        <div class="accordion-item">
                                            <div class="accordion-header">
                                                <button aria-controls="collapse-{{ $user->getId() }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-{{ $user->getId() }}" data-bs-toggle="collapse">
                                                    {{ __('general.qualifikationen_anzeigen') }}
                                                    <div class="accordion-button-toggle">
                                                        <x-icon name="chevron-down" />
                                                    </div>
                                                </button>

                                            </div>
                                            <div class="accordion-collapse collapse" data-bs-parent="#qualifications-accordion-{{ $user->getId() }}" id="collapse-{{ $user->getId() }}">
                                                <div class="accordion-body">
                                                    <ul style="list-style: none; padding-left: 0;">
                                                        @foreach ($qualifications as $qualification)
                                                            @php
                                                                $url = '';
                                                                $has_qualification = $user->account->qualifications->firstWhere('qualification_id', $qualification->getId());
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
                                                                        if (!empty($has_qualification->pivot->training_id)) {
                                                                            $url = route('ausbildung.show', $has_qualification->pivot->training_id);
                                                                        }
                                                                    @endphp
                                                                    <a @if (!empty($url)) href="{{ $url }}" @endif data-bs-title="{{ __('general.seit', ['date' => now()->parse($has_qualification->pivot->created_at)->format('d.m.Y')]) }}" data-bs-toggle="tooltip">
                                                                        <x-icon :classes="['text-success']" name="check" />
                                                                    </a>
                                                                @endif
                                                                {{ $qualification->getName() }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->isTrainer() ? __('general.ja') : __('general.nein') }}
                                </td>
                                <td class="text-center">
                                    @if ($user->activeTrainingBan)
                                        <a data-bs-target="#training-ban-reason-{{ $user->getId() }}" data-bs-toggle="modal">
                                            <x-icon :classes="['text-danger', 'cursor-pointer']" :hovertext="__('general.ausbildungssperre_bis', [
                                                'date_from' => $user->activeTrainingBan->getDateFrom()->format('d.m.Y'),
                                                'date_to' => $user->activeTrainingBan->getDateTo()->format('d.m.Y'),
                                            ])" name="ban" />
                                        </a>
                                        @include('training.modals.ban_show')
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if (!empty($user->account->qualifications->count()))
                                        @can('user.qualifications.remove')
                                            <a class="btn btn-sm btn-primary" data-bs-target="#remove-user-qualification-{{ $user->getId() }}" data-bs-toggle="modal">
                                                <x-icon :classes="['text-danger']" :hovertext="__('general.qualifikation_entfernen')" name="circle-minus" />
                                            </a>
                                            @include('training.modals.remove_qualification', [
                                                'user' => $user,
                                                'qualifications' => $user->account->qualifications,
                                            ])
                                        @endcan
                                    @endif
                                    @can('user.qualifications.assign')
                                        <a class="btn btn-sm btn-primary" data-bs-target="#add-user-qualification-{{ $user->getId() }}" data-bs-toggle="modal">
                                            <x-icon :classes="['text-success']" :hovertext="__('general.qualifikation_zuweisen')" name="plus" />
                                        </a>
                                        @include('training.modals.add_qualification', [
                                            'user' => $user,
                                        ])
                                    @endcan
                                    @can('trainingban.assign')
                                        @if (empty($user->activeTrainingBan))
                                            <a class="btn btn-sm btn-primary" data-bs-target="#add-training-ban-{{ $user->getId() }}" data-bs-toggle="modal">
                                                <x-icon :classes="['text-danger', 'cursor-pointer']" :hovertext="__('general.ausbildungssperre_verteilen')" name="ban" />
                                            </a>
                                            @include('training.modals.ban', ['user' => $user])
                                        @endif
                                    @endcan
                                    @canany(['usermanagement.edit.personal_data', 'usermanagement.edit.account_data', 'usermanagement.edit.permissions'])
                                        <a class="btn btn-sm btn-primary" href="{{ route('usermanagement.edit', $user) }}">
                                            <x-icon :classes="['text-white']" :hovertext="__('general.bearbeiten')" name="edit" />
                                        </a>
                                    @endcan
                                    @can('usermanagement.delete')
                                        <x-modal body_classes="text-center" form_action="{{ route('usermanagement.destroy', [$user]) }}" id="delete-user-{{ $user->getId() }}">

                                            <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
                                            <x-slot:body>
                                                <div class="text-center">
                                                    <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
                                                </div>
                                                <p class="text-warning">{!! __('general.benutzer_loeschen_confirm', [
                                                    'name' => $user->getFullName(),
                                                    'br' => '<br/>',
                                                ]) !!}
                                                </p>
                                            </x-slot:body>
                                            <x-slot:footer>
                                                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
                                                <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
                                            </x-slot:footer>
                                        </x-modal>
                                        <a class="btn btn-sm btn-danger" data-bs-target="#delete-user-{{ $user->getId() }}" data-bs-toggle="modal">
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
