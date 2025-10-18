<x-modal form_action="{{ route('ausbildung.abschliessen', $training) }}" id="lehrgangAbschliessenModal">
    <x-slot:title>{{ __('general.lehrgang_abschliessen') }}</x-slot:title>
    <x-slot:body>
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
                                <input class="form-check-input" id="cancelled" name="cancelled" type="checkbox" value="1" x-model="cancel">
                                <span class="ms-2 form-check-label" for="cancelled">Ja</span>
                            </label>
                        </div>
                        <div class="form-floating" x-bind:class="{ 'd-none': cancel == false }">
                            <textarea class="form-control" id="cancelled_notice" name="cancelled_notice"></textarea>
                            <label class="form-label">{{ __('general.grund') }}</label>
                        </div>
                    </div>
                </div>
            </li>
            @foreach ($training->getSortParticipants() as $participant)
                <li class="list-group-item" x-data="{ note: false, present: false, logged_out: false }">
                    <div class="row">
                        <div class="col-6 d-flex align-items-center">
                            <p class="text-center w-100">{{ $participant->getFullName() }}</p>
                        </div>
                        <div class="col-6 text-start">
                            <div>
                                <label class="form-check form-switch" x-bind:class="{ 'd-none': logged_out == true }">
                                    <input class="form-check-input" id="anwesend" name="anwesend[{{ $participant->getId() }}]" type="checkbox" value="1" x-model="present">
                                    <span class="ms-2 form-check-label" for="anwesend">{{ __('general.anwesend') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch" x-bind:class="{ 'd-none': logged_out == true }">
                                    <input class="form-check-input" id="bestanden" name="bestanden[{{ $participant->getId() }}]" type="checkbox" value="1">
                                    <span class="ms-2 form-check-label" for="bestanden">{{ __('general.bestanden') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch" x-bind:class="{ 'd-none': present == true }">
                                    <input class="form-check-input" id="abgemeldet" name="abgemeldet[{{ $participant->getId() }}]" type="checkbox" value="1" x-model="logged_out">
                                    <span class="ms-2 form-check-label" for="abgemeldet">{{ __('general.abgemeldet') }}</span>
                                </label>
                            </div>
                            <div>
                                <label class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" value="1" x-model="note">
                                    <span class="ms-2 form-check-label">{{ __('general.notizen') }}</span>
                                </label>
                            </div>
                            <div class="form-floating" x-bind:class="{ 'd-none': note == false }">
                                <textarea class="form-control" id="notices" name="notices[{{ $participant->getId() }}]"></textarea>
                                <label class="form-label">{{ __('general.grund') }}</label>
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
