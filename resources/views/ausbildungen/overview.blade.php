@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mb-2 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungen') }}
                </div>
                <h2 class="page-title">
                    {{ $training->getName() }}
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
                        <p><b>{{ __('general.lehrgangs_id') }}: </b>{{ $training->getId() }}</p>
                        <p><b>{{ __('general.bezeichnung') }}: </b> {{ $training->getName() }}</p>
                    </div>
                </x-slot:header>
                <x-slot:body>
                    <form action="{{ route('ausbildung.update', $training) }}" class="space-y" method="POST" x-data="{
                        trainer_id: @js($training->getTrainerId()),
                        date: @js($training->getDate()->format('Y-m-d')),
                        time: @js($training->getTime()->format('H:i')),
                        min_participants: @js($training->getMinParticipants()),
                        max_participants: @js($training->getMaxParticipants()),
                        meeting_point: @js($training->getMeetingPoint()),
                        additional_informations: @js($training->getAdditionalInformation())
                    }" x-init="original = { trainer_id: trainer_id, date: date, time: time, min_participants: min_participants, max_participants: max_participants, meeting_point: meeting_point, additional_informations: additional_informations }">
                        @csrf
                        @if (!$training->isCompleted())
                            <div class="row">
                                <div class="col">
                                    <x-forms.select label="{{ __('general.ausbilder') }}" name="trainer_id" required x-model="trainer_id">
                                        <option disabled selected value="">-- {{ __('general.bitte_waehlen') }} --</option>
                                        @foreach ($trainers as $trainer)
                                            <option @selected($trainer->getId() == $training->getTrainerId()) value="{{ $trainer->getId() }}">
                                                {{ $trainer->getFullName() }}
                                                {{ $training->getTrainerId() == $trainer->getId() ? '(' . __('general.standard') . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </x-forms.select>
                                </div>
                                @if ($training->getWarningInactiveTrainer())
                                    <div class="col-auto d-flex align-items-center">
                                        <x-icon :classes="['text-danger']" :hovertext="$training->getWarningInactiveTrainer()" name="alert-triangle" />
                                    </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col">
                                    <x-forms.input name="date" required type="date" value="{{ $training->getDate()->format('Y-m-d') }}" x-model="date">
                                        {{ __('general.datum') }}
                                    </x-forms.input>
                                </div>
                                <div class="col">
                                    <x-forms.input name="time" required type="time" value="{{ $training->getTime()->format('H:i') }}" x-model="time">
                                        {{ __('general.uhrzeit') }}
                                    </x-forms.input>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <x-forms.input name="min_participants" required type="number" value="{{ $training->getMinParticipants() }}" x-model="min_participants">
                                        {{ __('general.minimale_teilnehmer') }}
                                    </x-forms.input>
                                </div>
                                <div class="col">
                                    <x-forms.input name="max_participants" required type="number" value="{{ $training->getMaxParticipants() }}" x-model="max_participants">
                                        {{ __('general.maximale_teilnehmer') }}
                                    </x-forms.input>
                                </div>
                            </div>

                            <x-forms.input name="meeting_point" required value="{{ $training->getMeetingPoint() }}" x-model="meeting_point">
                                {{ __('general.treffpunkt') }}
                            </x-forms.input>

                            <div class="form-floating">
                                <textarea class="form-control" id="additional_informations" name="additional_informations" style="min-height:80px" x-model="additional_informations">{{ $training->getAdditionalInformation() }}</textarea>
                                <label>{{ __('general.zusaetzliche_informationen') }}</label>
                            </div>
                            <div>
                                <x-discord-notification />
                            </div>
                            @can('is_trainer')
                                <div class="d-none" x-bind:class="{
                                    'd-block': trainer_id != original.trainer_id || date != original.date || time != original.time || min_participants != original.min_participants || max_participants != original.max_participants || meeting_point != original.meeting_point || additional_informations != original.additional_informations,
                                    'd-none': trainer_id == original.trainer_id && date == original.date && time == original.time && min_participants == original.min_participants && max_participants == original.max_participants && meeting_point == original.meeting_point && additional_informations == original.additional_informations
                                }">
                                    <button class="btn btn-primary" type="submit">{{ __('general.speichern') }}</button>
                                </div>
                            @endcan
                        @else
                            <div>
                                <p><b>{{ __('general.ausbilder') }}:</b> {{ $training->getTrainerName() }}</p>
                                <p>{{ __('general.datum') }}: {{ $training->getFormatedDate() }}</p>
                            </div>
                        @endif
                    </form>
                    <div class="text-end">
                        @if (!$training->isCompleted())
                            @can('is_trainer')
                                @if (now()->addMinutes((int) config('settings.training_complete_minutes')) > $training->getDeadlineTime())
                                    <button class="btn btn-success" data-bs-target="#lehrgangAbschliessenModal" data-bs-toggle="modal" type="button">
                                        {{ __('general.lehrgang_abschliessen') }}
                                    </button>
                                    @include('ausbildungen.modals.training_complete')
                                @endif
                            @endcan
                        @else
                            <p class="text-success">{{ __('general.lehrgang_abgeschlossen') }}</p>
                        @endif
                    </div>
                </x-slot:body>
            </x-card>
        </div>

        @can('trainings.participants.show')
            <div class="mt-4 mb-4 row">
                <x-card>
                    <x-slot:header>
                        <h3 class="card-title">{{ __('general.teilnehmer') }} ({{ $training->getCountParticipants() }})</h3>
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
                            @forelse ($training->getSortParticipants() as $participant)
                                <tr>
                                    <td>
                                        {{ $participant->account->getSalutation() }} {{ $participant->getFullName() }} ({{ $participant->account->getDefaultFraction()->getShortName() }})
                                        @if (!empty($participant->account))
                                            @can('usermanagement.index')
                                                <a href="{{ route('profile.show', $participant->account) }}" target="_blank">
                                                    <x-icon name="external-link" />
                                                </a>
                                            @endcan
                                        @endif
                                    </td>
                                    <td>
                                        {{ $participant->getBirthDate() }} in {{ $participant->account?->getBirthLocation() }}
                                    </td>
                                    <td>
                                        {{ $participant->getCreated()->format('d.m.Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="accordion-flush" id="qualifications-accordion-{{ $participant->getId() }}">
                                            <div class="accordion-item">
                                                <div class="accordion-header">
                                                    <button aria-controls="collapse-{{ $participant->getId() }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-{{ $participant->getId() }}" data-bs-toggle="collapse">
                                                        {{ __('general.qualifikationen_anzeigen') }}
                                                        <div class="accordion-button-toggle">
                                                            <x-icon name="chevron-down" />
                                                        </div>
                                                    </button>

                                                </div>
                                                <div class="accordion-collapse collapse" data-bs-parent="#qualifications-accordion-{{ $participant->getId() }}" id="collapse-{{ $participant->getId() }}">
                                                    <div class="accordion-body">
                                                        <ul style="list-style: none; padding-left: 0;">
                                                            @forelse($participant?->account?->qualifications ?? [] as $qualification)
                                                                <li>
                                                                    {{ $qualification->getName() }}
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
                                        @if (!$training->isCompleted())
                                            @can('is_trainer')
                                                <button class="btn btn-sm btn-danger" data-bs-target="#confirm-remove-teilnehmer-{{ $participant->getId() }}" data-bs-toggle="modal" type="button">
                                                    <x-icon name="trash" />
                                                </button>

                                                <x-modal form_action="{{ route('ausbildung.teilnehmer.entfernen', $participant) }}" id="confirm-remove-teilnehmer-{{ $participant->getId() }}">
                                                    <x-slot:title>
                                                        {{ __('general.teilnehmer_entfernen') . ': ' . $participant->getFullName() }}
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
                                            @endcan
                                        @else
                                            <span class="ms-2">
                                                @if ($participant->getNotices())
                                                    <x-icon :hovertext="$participant->getNotices()" name="info-circle" />
                                                @endif
                                                {!! $participant->getOutputBadge() !!}
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
                        @if (!$training->isCompleted())
                            @can('is_trainer')
                                <h4>{{ __('general.teilnehmer_hinzufuegen') }}:</h4>
                                <form action="{{ route('ausbildung.teilnehmer.hinzufuegen', $training) }}" class="space-y" method="POST">
                                    @csrf
                                    <x-forms.select label="{{ __('general.teilnehmer') }}" multiple name="user_ids" required>
                                        @foreach ($users_not_in_training as $user)
                                            <option value="{{ $user->getId() }}">{{ $user->getFullName() }} ({{ $user->account->getDefaultFraction()->short_name }})</option>
                                        @endforeach
                                    </x-forms.select>

                                    <div>
                                        <button class="btn btn-primary" type="submit">{{ __('general.teilnehmer_hinzufuegen') }}</button>
                                    </div>
                                </form>
                            @endcan
                        @endif
                    </x-slot:body>
                </x-card>
            </div>
        @endcan
    </div>
@endsection
