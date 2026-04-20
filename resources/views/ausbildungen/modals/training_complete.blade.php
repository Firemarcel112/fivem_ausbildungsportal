@use('App\DTO\TrainingViewData')
@php
    /** @var TrainingViewData $training */
@endphp
@props([
    'training' => null,
])
<x-modal form_action="{{ route('ausbildung.abschliessen', $training->id) }}" id="lehrgangAbschliessenModal">
    <x-slot:title>{{ __('general.lehrgang_abschliessen') }}</x-slot:title>
    <x-slot:body>
        @if ($training->can_generate_certificate)
            <x-alert :static="true">
                {{ __('general.zertifikat_wird_automatisch_nach_beendigung_erstellt') }}
            </x-alert>
        @else
            <x-alert :static="true" type="warning">
                {{ __('general.zertifikat_wird_nicht_automatisch_nach_beendigung_erstellt') }}
            </x-alert>
        @endif
        <x-forms.button>
            {{ __('general.lehrgang_abschliessen') }}
        </x-forms.button>

        <ul class="list-group" x-data="{ cancel: false }">
            <li class="list-group-item">
                <div class="row">
                    <div class="col-6 d-flex align-items-center">
                        <p class="text-center w-100">{{ __('general.ausbildung_entfaellt') }}</p>
                    </div>
                    <div class="col-6 text-start">
                        <div>
                            <label class="form-check form-switch">
                                <input class="form-check-input" id="cancelled" name="cancelled" type="hidden" value="0">
                                <input class="form-check-input" id="cancelled" name="cancelled" type="checkbox" value="1" x-model="cancel">
                                <span class="ms-2 form-check-label" for="cancelled">{{ __('general.ja') }}</span>
                            </label>
                        </div>
                        <div class="form-floating" x-bind:class="{ 'd-none': cancel == false }">
                            <textarea class="form-control" id="cancelled_notice" name="cancelled_notice"></textarea>
                            <label class="form-label">{{ __('general.grund') }}</label>
                        </div>
                    </div>
                </div>
            </li>
            @foreach ($training->participants as $participant)
                <li class="list-group-item" x-data="{
                    note: false,
                    present: false,
                    logged_out: false,
                    passed: false,
                }">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <p class="text-center w-100">{{ $participant->full_name }} ({{ $participant->account_id }})</p>
                        </div>
                        <div class="col-6 text-start">
                            <div>
                                <label class="form-check form-switch" x-show="!logged_out">
                                    <input name="participants[{{ $participant->id }}][present]" type="hidden" value="0">
                                    <input class="form-check-input" id="present" name="participants[{{ $participant->id }}][present]" type="checkbox" value="1" x-bind:checked="passed" x-model="present">
                                    <span class="ms-2 form-check-label" for="present">{{ __('general.anwesend') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch" x-show="!logged_out">
                                    <input name="participants[{{ $participant->id }}][passed]" type="hidden" value="0">
                                    <input class="form-check-input" id="passed" name="participants[{{ $participant->id }}][passed]" type="checkbox" value="1" x-model="passed">
                                    <span class="ms-2 form-check-label" for="passed">{{ __('general.bestanden') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch" x-show="!present && !passed">
                                    <input name="participants[{{ $participant->id }}][signed_out]" type="hidden" value="0">
                                    <input class="form-check-input" id="sign_out" name="participants[{{ $participant->id }}][signed_out]" type="checkbox" value="1" x-model="logged_out">
                                    <span class="ms-2 form-check-label" for="sign_out">{{ __('general.abgemeldet') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" value="1" x-model="note">
                                    <span class="ms-2 form-check-label">{{ __('general.notizen') }}</span>
                                </label>
                            </div>
                            <div class="form-floating" x-bind:class="{ 'd-none': note == false }">
                                <textarea class="form-control" id="notices" name="participants[{{ $participant->id }}][notices]"></textarea>
                                <label class="form-label">{{ __('general.notizen') }}</label>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

    </x-slot:body>
    <x-slot:footer>
        <x-forms.button>
            {{ __('general.lehrgang_abschliessen') }}
        </x-forms.button>
    </x-slot:footer>
</x-modal>
