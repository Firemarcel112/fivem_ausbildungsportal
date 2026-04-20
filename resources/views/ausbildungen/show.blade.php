@use('App\DTO\TrainingViewData')
@use('App\DTO\SimpleUserViewData')
@use('App\DTO\TrainingParticipantViewData')
@use('Illuminate\Support\Collection')
@php
    /** @var TrainingViewData $training */
    /** @var Collection<TrainingParticipantViewData> $participants */
    /** @var Collection<SimpleUserViewData> $users_not_in_training */
    /** @var SimpleUserViewData $auth_user */
@endphp

@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mb-2 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungen') }}
                </div>
                <h2 class="page-title">
                    {{ $training->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
        </div>
        @include('default.alerts')
        <div class="mt-2 row">
            <x-card>
                <x-slot:header>
                    <div class="d-block">
                        <p><b>{{ __('general.lehrgangs_id') }}: </b>{{ $training->id }}</p>
                        <p><b>{{ __('general.bezeichnung') }}: </b> {{ $training->name }}</p>
                    </div>
                </x-slot:header>
                <x-slot:body>
                    <form action="{{ route('ausbildung.update', $training->id) }}" class="space-y" method="POST" x-data="{
                        trainer_id: @js($training->trainer_id),
                        date: @js($training->date->format('Y-m-d')),
                        time: @js($training->time->format('H:i')),
                        min_participants: @js($training->min_participants),
                        max_participants: @js($training->max_participants),
                        meeting_point: @js($training->meeting_point),
                        additional_informations: @js($training->additional_informations)
                    }" x-init="original = { trainer_id: trainer_id, date: date, time: time, min_participants: min_participants, max_participants: max_participants, meeting_point: meeting_point, additional_informations: additional_informations }">
                        @csrf
                        @if (!$training->is_completed)
                            <div class="row">
                                <div class="col">
                                    <x-forms.select.trainers :selected-id="$training->trainer_id" x-model="trainer_id" />
                                </div>
                                @if ($training->trainer_is_inactive)
                                    <div class="col-auto d-flex align-items-center">
                                        <x-icon :classes="['text-danger']" :hovertext="$training->trainer_name . ' - ' . __('general.ausbilder_nicht_mehr_aktiv')" name="alert-triangle" />
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col">
                                    <x-forms.input name="date" required type="date" value="{{ $training->date->format('Y-m-d') }}" x-model="date">
                                        {{ __('general.datum') }}
                                    </x-forms.input>
                                </div>
                                <div class="col">
                                    <x-forms.input name="time" required type="time" value="{{ $training->time->format('H:i') }}" x-model="time">
                                        {{ __('general.uhrzeit') }}
                                    </x-forms.input>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <x-forms.input name="min_participants" required type="number" value="{{ $training->min_participants }}" x-model="min_participants">
                                        {{ __('general.minimale_teilnehmer') }}
                                    </x-forms.input>
                                </div>
                                <div class="col">
                                    <x-forms.input name="max_participants" required type="number" value="{{ $training->max_participants }}" x-model="max_participants">
                                        {{ __('general.maximale_teilnehmer') }}
                                    </x-forms.input>
                                </div>
                            </div>

                            <x-forms.input name="meeting_point" required value="{{ $training->meeting_point }}" x-model="meeting_point">
                                {{ __('general.treffpunkt') }}
                            </x-forms.input>

                            <div class="form-floating">
                                <textarea class="form-control" id="additional_informations" name="additional_information" style="min-height:80px" x-model="additional_informations">{{ $training->additional_informations }}</textarea>
                                <label>{{ __('general.zusaetzliche_informationen') }}</label>
                            </div>
                            <div>
                                <x-discord-notification />
                            </div>
                            @if ($auth_user->is_trainer)
                                <div class="d-none" x-bind:class="{
                                    'd-block': trainer_id != original.trainer_id || date != original.date || time != original.time || min_participants != original.min_participants || max_participants != original.max_participants || meeting_point != original.meeting_point || additional_informations != original.additional_informations,
                                    'd-none': trainer_id == original.trainer_id && date == original.date && time == original.time && min_participants == original.min_participants && max_participants == original.max_participants && meeting_point == original.meeting_point && additional_informations == original.additional_informations
                                }">
                                    <button class="btn btn-primary" type="submit">{{ __('general.speichern') }}</button>
                                </div>
                            @endif
                        @else
                            <div>
                                <p><b>{{ __('general.ausbilder') }}:</b> {{ $training->trainer_name }}</p>
                                <p>{{ __('general.datum') }}: {{ $training->full_time }}</p>
                            </div>
                        @endif
                    </form>
                    <div class="text-end">
                        @if (!$training->is_completed && $auth_user->is_trainer)
                            @if ($training->can_completed)
                                <button class="btn btn-success" data-bs-target="#lehrgangAbschliessenModal" data-bs-toggle="modal" type="button">
                                    {{ __('general.lehrgang_abschliessen') }}
                                </button>
                                @include('ausbildungen.modals.training_complete', [
                                    'training' => $training,
                                ])
                            @endif
                        @else
                            <p class="text-success">{{ __('general.lehrgang_abgeschlossen') }}</p>
                        @endif
                    </div>
                </x-slot:body>
            </x-card>
        </div>

        @if ($training->can_show_participants)
            <div class="mt-4 mb-4 row">
                <x-card>
                    <x-slot:header>
                        <h3 class="card-title">{{ __('general.teilnehmer') }} ({{ $training->count_participants }})</h3>
                    </x-slot:header>
                    <x-slot:body>
                        <table class="table table-vcenter">
                            <thead>
                                <th>{{ __('general.name') }}</th>
                                <th>{{ __('general.geburtsdatum') }}</th>
                                <th>{{ __('general.angemeldet') }}</th>
                                <th>{{ __('general.qualifikation') }}</th>
                                <th></th>
                            </thead>
                            @forelse ($training->participants as $participant)
                                <tr>
                                    <td>
                                        {{ $participant->salutation }} {{ $participant->full_name }} ({{ $participant->default_fraction_short_name }})
                                        @if (!empty($participant->user_id) && $participant->can_view_profile)
                                            <a href="{{ route('profile.show', $participant->user_id) }}" target="_blank">
                                                <x-icon name="external-link" />
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $participant->date_of_birth->format('d.m.Y') }} in {{ $participant->birth_location }}
                                    </td>
                                    <td>
                                        {{ $participant->sign_at }}
                                    </td>
                                    <td>
                                        <div class="accordion-flush" id="qualifications-accordion-{{ $participant->id }}">
                                            <div class="accordion-item">
                                                <div class="accordion-header">
                                                    <button aria-controls="collapse-{{ $participant->id }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-{{ $participant->id }}" data-bs-toggle="collapse">
                                                        {{ __('general.qualifikationen_anzeigen') }}
                                                        <div class="accordion-button-toggle">
                                                            <x-icon name="chevron-down" />
                                                        </div>
                                                    </button>

                                                </div>
                                                <div class="accordion-collapse collapse" data-bs-parent="#qualifications-accordion-{{ $participant->id }}" id="collapse-{{ $participant->id }}">
                                                    <div class="accordion-body">
                                                        <ul style="list-style: none; padding-left: 0;">
                                                            @forelse($participant->qualification_names ?? [] as $qualification)
                                                                <li>
                                                                    {{ $qualification->label }}
                                                                </li>
                                                            @empty
                                                                <li>{{ __('general.keine_qualifikation') }}</li>
                                                            @endforelse
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        @if (!$training->is_completed && $auth_user->is_trainer)
                                            <button class="btn btn-sm btn-danger" data-bs-target="#confirm-remove-teilnehmer-{{ $participant->id }}" data-bs-toggle="modal" type="button">
                                                <x-icon name="trash" />
                                            </button>

                                            <x-modal form_action="{{ route('ausbildung.teilnehmer.entfernen', $participant->id) }}" id="confirm-remove-teilnehmer-{{ $participant->id }}">
                                                <x-slot:title>
                                                    {{ __('general.teilnehmer_entfernen') . ': ' . $participant->full_name }}
                                                </x-slot:title>
                                                <x-slot:footer>
                                                    <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">
                                                        {{ __('general.abbrechen') }}
                                                    </button>
                                                    <button class="btn btn-danger" type="submit">
                                                        {{ __('general.entfernen') }}
                                                    </button>
                                                </x-slot:footer>
                                            </x-modal>
                                        @else
                                            <span class="ms-2">
                                                @if (!empty($participant->notices))
                                                    <x-icon :hovertext="$participant->notices" name="info-circle" />
                                                @endif
                                                <span class="badge {{ $participant->status->getBadgeColorClass() }} text-white">{{ $participant->status->getLabel() }}</span>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-center" colspan="5">
                                        {{ __('general.keine_teilnehmer') }}
                                    </td>
                                </tr>
                            @endforelse
                        </table>
                        @if (!$training->is_completed && $auth_user->is_trainer)
                            <h4>{{ __('general.teilnehmer_hinzufuegen') }}:</h4>
                            <form action="{{ route('ausbildung.teilnehmer.hinzufuegen', $training->id) }}" class="space-y" method="POST">
                                @csrf
                                <x-forms.select label="{{ __('general.teilnehmer') }}" multiple name="account_ids" required>
                                    @foreach ($users_not_in_training as $user)
                                        <option value="{{ $user->account_id }}">{{ $user->full_name }} ({{ $user->fraction_data['default']['short_name'] }}) ({{ $user->account_id }})</option>
                                    @endforeach
                                </x-forms.select>

                                <div>
                                    <button class="btn btn-primary" type="submit">{{ __('general.teilnehmer_hinzufuegen') }}</button>
                                </div>
                            </form>
                        @endif
                    </x-slot:body>
                </x-card>
            </div>
        @endif
    </div>
@endsection
