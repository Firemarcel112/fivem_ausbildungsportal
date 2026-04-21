@php
    /** @var \App\DTO\TrainingViewData $training **/
    /** @var int $enroll_deadline_in_minutes */
    /** @var App\Models\User|null $user */
    /** @var int|null $user_id */
@endphp

@props([
    'user' => auth()->user(),
    'user_id' => auth()->user()?->id,
])


<x-card :classes="['h-100']" card-body-classes="p-2 ps-4">
    <x-slot:header>
        <div class="col">
            @empty(!$training->trainer_url)
                <p class="card-title">
                    <a class="d-inline-flex align-items-center" href="{{ $training->trainer_url }}">
                        {{ $training->getName() }}
                        <x-icon name="external-link" width-height="16" /></a>
                </p>
            @else
                <p class="card-title">{{ $training->getName() }}</p>
            @endempty
        </div>
        <div class="col-auto">
            @if ($training->tooFewParticipants())
                <span>
                    <x-icon :classes="[
                        'text-danger' => $training->canEnrollWarning($enroll_deadline_in_minutes),
                        'cursor-pointer',
                    ]" :hovertext="__('general.zu_wenig_teilnehmer', ['br' => '<br>', 'min' => $training->min_participants])" name="alert-triangle" />
                </span>
            @endif
        </div>
    </x-slot:header>
    <x-slot:body>
        <div>
            <strong>{{ __('general.ausbilder') }}:</strong>
            {{ $training->trainer_name }}
        </div>
        <div>
            <strong>{{ __('general.treffpunkt') }}:</strong>
            {{ $training->meeting_point }}
        </div>
        <div>
            <strong>{{ __('general.uhrzeit') }}:</strong>
            {{ $training->time_output }}
        </div>
        <div>
            @can('trainings.participants.show')
                <a data-bs-target="#teilnehmer-{{ $training->id }}" data-bs-toggle="modal" style="text-decoration: underline; text-underline-offset: 2px; cursor: pointer;">
                    {{ __('general.teilnehmeranzahl') }}:
                    {{ $training->getOutputCountAllowedParticipants() }}
                </a>
            @else
                <strong>{{ __('general.teilnehmeranzahl') }}: </strong>
                {{ $training->count_participants }} /
                {{ $training->max_participants }}
            @endcan
            @can('trainings.participants.show')
                <x-modal id="teilnehmer-{{ $training->id }}">
                    <x-slot:title>{{ __('general.teilnehmerliste') }}:
                        {{ $training->name }}
                    </x-slot:title>
                    <x-slot:body>
                        <ul>
                            @foreach ($training->participants as $participant)
                                <li class="mb-2 list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="d-block">{{ $participant->full_name }} ({{ $participant->default_fraction_short_name }})
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
            @if (!empty($training->requirements))
                <details class="mt-2">
                    <summary><b>{{ __('general.voraussetzungen') }}</b></summary>
                    @foreach ($training->requirements as $fraction_name => $items)
                        <details class="my-2 ms-4">
                            <summary><b>{{ $fraction_name }}</b></summary>
                            <ul class="ms-2">
                                @php
                                    /** @var \App\DTO\SimpleListItem $item **/
                                @endphp
                                @foreach ($items as $item)
                                    <li>{{ $item->label }}</li>
                                @endforeach
                            </ul>
                        </details>
                    @endforeach
                </details>
            @endif
            @if (!empty($training->additional_informations))
                <hr class="m-2 mt-4">
                <p class="fw-bold">{{ __('general.zusaetzliche_informationen') }}</p>
                {{ $training->additional_informations }}
            @endif
        </div>
    </x-slot:body>
    <x-slot:footer>
        @auth
            @if ($training->can_register && !$training->isRegistered($user_id) && !$user->activeTrainingBan)
                <form action="{{ route('training.register', $training->id) }}" method="POST">
                    @csrf
                    <button class="me-2 btn btn-primary" type="submit">{{ __('general.anmelden') }}
                    </button>
                </form>
            @elseif($training->isRegistered())
                <form action="{{ route('training.sign_out', $training->id) }}" method="POST">
                    @csrf
                    <button class="me-2 btn btn-danger" type="submit">{{ __('general.abmelden') }}
                    </button>
                </form>
            @else
                <p class="text-danger d-flex align-items-center">
                    <x-icon name="info-circle" /> <span class="ms-2">{{ __('general.anmeldung_geschlossen') }}</span>
                </p>
            @endif
        @else
            <a href="{{ route('login.index') }}">{{ __('general.du_musst_angemeldet_sein') }}</a>
        @endauth
    </x-slot:footer>
</x-card>
