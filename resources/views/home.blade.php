@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="row mb-2 g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungen') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list d-flex flex-column flex-md-row">
                    @auth
                        <a class="mb-2 btn btn-primary mb-md-0 me-md-2" data-bs-target="#training-request" data-bs-toggle="modal">
                            <span>{{ __('general.ausbildungswunsch') }}</span>
                        </a>
                        @include('ausbildungen.modals.request')
                    @endauth

                    @can('trainings.store')
                        <div class="input-group w-auto">
                            <x-trainings.modal.create />
                            @can('announcements')
                                @include('ausbildungen.modals.announcement')
                                <div class="btn btn-primary dropdown-toggle dropdown-toggle-spli" data-bs-toggle="dropdown"></div>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" data-bs-target="#ankuendigung_erstellen" data-bs-toggle="modal">{{ __('general.ankuendigung_erstellen') }}</a>
                                </div>
                            @endcan
                        </div>
                    @endcan
                </div>
            </div>
            @include('default.alerts')
            @if (auth()->check() && auth()->user()->activeTrainingBan)
                @php
                    $training_ban = auth()->user()->activeTrainingBan;
                @endphp
                <x-alert :static="true" type="error">
                    <div class="cursor-pointer" data-bs-target="#training_ban_reason_modal" data-bs-toggle="modal">
                        {{ __('general.ausbildungssperre_bis', [
                            'date_from' => $training_ban->getDateFrom()->format('d.m.Y'),
                            'date_to' => $training_ban->getDateTo()->format('d.m.Y'),
                        ]) }}
                        -
                        {{ __('general.ausgestellt_von', [
                            'name' => $training_ban->getIssuerName(),
                        ]) }}
                        <br>
                        <a>{{ __('general.fuer_begruendung_klicken') }}</span>
                    </div>
                </x-alert>
                <x-modal id="training_ban_reason_modal">
                    <x-slot:title>
                        {{ __('general.ausbildungssperre_bis', [
                            'date_from' => $training_ban->getDateFrom()->format('d.m.Y'),
                            'date_to' => $training_ban->getDateTo()->format('d.m.Y'),
                        ]) }}
                    </x-slot:title>
                    <x-slot:body>
                        <p>
                            {{ __('general.ausgestellt_von', [
                                'name' => $training_ban->getIssuerName(),
                            ]) }}
                        </p>
                        <p>{{ __('general.grund') }}</p>
                        {{ nl2br($training_ban->getReason()) }}
                    </x-slot:body>
                </x-modal>
            @endif

        </div>
        <div class="row">
            @forelse($trainings ?? [] as $date => $training_model)
                @php
                    $date = new DateTime($date);
                    $day_of_week = __('general.tag.' . $date->format('N'));
                @endphp
                <h2 class="fw-bold">{{ $day_of_week }} {{ $date->format('d.m.Y') }}</h2>
                <div class="row g-2 mb-2">
                    @foreach ($training_model as $training)
                        <div class="col-md-4 col-12 h-auto">
                            <x-card :classes="['h-100']" card-body-classes="p-2 ps-4">
                                <x-slot:header>
                                    <div class="col">
                                        <p class="card-title"> {!! $training->getName(true) !!}</p>
                                    </div>
                                    <div class="col-auto">
                                        @if ($training->getCountParticipants() < $training->getMinParticipants())
                                            <span>
                                                <x-icon :classes="[
                                                    'text-danger' => now() > $training->getDeadlineTime()->subMinutes((int) config('settings.enroll_deadline')),
                                                    'cursor-pointer',
                                                ]" :hovertext="__('general.zu_wenig_teilnehmer', ['br' => '<br>', 'min' => $training->getMinParticipants()])" name="alert-triangle" />
                                            </span>
                                        @endif
                                    </div>
                                </x-slot:header>
                                <x-slot:body>
                                    <div>
                                        <strong>{{ __('general.ausbilder') }}:</strong>
                                        {{ $training->getTrainerName() }}
                                    </div>
                                    <div>
                                        <strong>{{ __('general.treffpunkt') }}:</strong>
                                        {{ $training->getMeetingPoint() }}
                                    </div>
                                    <div>
                                        <strong>{{ __('general.uhrzeit') }}:</strong>
                                        {{ $training->getTime()->format('H:i') }}
                                    </div>
                                    <div>
                                        @can('trainings.participants.show')
                                            <a data-bs-target="#teilnehmer-{{ $training->getId() }}" data-bs-toggle="modal" style="text-decoration: underline; text-underline-offset: 2px; cursor: pointer;">
                                                {{ __('general.teilnehmeranzahl') }}:
                                                {{ $training->getOutputCountAllowedParticipants() }}
                                            </a>
                                        @else
                                            <strong>{{ __('general.teilnehmeranzahl') }}: </strong>
                                            {{ $training->getCountParticipants() }} /
                                            {{ $training->getMaxParticipants() }}
                                        @endcan
                                        @auth
                                            @can('trainings.participants.show')
                                                <x-modal id="teilnehmer-{{ $training->getId() }}">
                                                    <x-slot:title>{{ __('general.teilnehmerliste') }}:
                                                        {!! $training->getName() !!}
                                                    </x-slot:title>
                                                    <x-slot:body>
                                                        <ul>
                                                            @foreach ($training->getSortParticipants() as $participant)
                                                                <li class="mb-2 list-group-item d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <span class="d-block">{{ $participant->getFullName() }} ({{ $participant->account->getDefaultFraction()->getShortName() }})
                                                                        </span>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </x-slot:body>
                                                    <x-slot:footer>
                                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.schliessen') }}</button>
                                                    </x-slot:footer>
                                                </x-modal>
                                            @endcan
                                        @endauth
                                        @if ($training->requirements->count() != 0)
                                            <details>
                                                <summary><b>{{ __('general.voraussetzungen') }}:</b></summary>
                                                @foreach ($fractions as $fraction)
                                                    @continue($training->requirements->where('fraction_id', $fraction->getKey())->count() == 0)

                                                    <details class="mt-2 mb-2 ms-4">
                                                        <summary><b>{{ $fraction->full_name }}</b></summary>
                                                        <ul class="ms-2">
                                                            @foreach ($training->requirements as $requirement)
                                                                @continue($requirement->getFractionId() != $fraction->getKey())
                                                                <li>{{ $requirement->getName() }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </details>
                                                @endforeach
                                            </details>
                                        @endif
                                        @if (!empty($training->getAdditionalInformation()))
                                            <hr class="m-2 mt-4">
                                            <p class="fw-bold">{{ __('general.zusaetzliche_informationen') }}</p>
                                            {{ $training->getAdditionalInformation() }}
                                        @endif
                                    </div>
                                </x-slot:body>
                                <x-slot:footer>
                                    @auth
                                        @if ($training->canRegister() && !$training->isRegistered())
                                            @if (!auth()?->user()?->activeTrainingBan)
                                                <form action="{{ route('training.register', $training->getId()) }}" method="POST">
                                                    @csrf
                                                    <button class="me-2 btn btn-primary" type="submit">{{ __('general.anmelden') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @elseif($training->isRegistered())
                                            <form action="{{ route('training.sign_out', $training->getId()) }}" method="POST">
                                                @csrf
                                                @if ($training->canRegister(true))
                                                    <button class="me-2 btn btn-danger" type="submit">{{ __('general.abmelden') }}
                                                    </button>
                                                @endif
                                            </form>
                                        @else
                                            <p class="text-danger d-flex align-items-center">
                                                <x-icon name="info-circle" /> <span class="ms-2">{{ __('general.anmeldung_geschlossen') }}</span>
                                            </p>
                                        @endif
                                    @else
                                        {{ __('general.du_musst_angemeldet_sein') }}
                                    @endauth
                                </x-slot:footer>
                            </x-card>
                        </div>
                    @endforeach
                </div>

            @empty
                <x-alert :static="true" type="warning">{{ __('general.aktuell_keine_ausbildungen') }}</x-alert>
            @endforelse
        </div>
    </div>
@endsection
