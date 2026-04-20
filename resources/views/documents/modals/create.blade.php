@use('Illuminate\Support\Collection')
@use('App\DTO\SimpleListItem')
@php
    /** @var Collection<int, SimpleListItem> $accounts **/
    /** @var Collection<int, SimpleListItem> $trainers **/
@endphp
<x-modal form_action="{{ route('documents.store') }}" id="create-new-document">
    <x-slot:title>{{ __('general.zertifikat_erstellen') }}</x-slot:title>
    <x-slot:body>
        <div class="space-y" x-data="{
            participant: 0,
        }">
            <div class="row">
                <x-forms.select label="{{ __('general.ausbilder') }}" name="trainer" required x-model="trainer">
                    @foreach ($trainers as $user)
                        <option @selected(old('trainer', request()->get('trainer')) == $user->id) value="{{ $user->id }}">
                            {{ $user->label }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>
            <div class="row">
                <x-forms.select label="{{ __('general.teilnehmer') }}" name="participant" x-model="participant">
                    <option selected value="">{{ __('general.teilnehmer_nicht_vorhanden') }}</option>
                    @foreach ($accounts as $account)
                        <option @selected(old('participant', request()->get('participant')) == $account->id) value="{{ $account->id }}">
                            {{ $account->label }} - {{ $account->description }}
                        </option>
                    @endforeach
                </x-forms.select>
            </div>
            <div class="row" x-show="participant == 0">
                <div class="col">
                    <x-forms.input name="first_name" x-bind:required="participant == 0">{{ __('general.vorname') }}</x-forms.input>
                </div>
                <div class="col">
                    <x-forms.input name="last_name" x-bind:required="participant == 0">{{ __('general.nachname') }}</x-forms.input>
                </div>
                <div class="col">
                    <x-forms.select.genders />
                </div>
            </div>
            <div class="row" x-show="participant == 0">
                <div class="col">
                    <x-forms.input name="date_of_birth" type="date" x-bind:required="participant == 0">{{ __('general.geburtsdatum') }}</x-forms.input>
                </div>
                <div class="col">
                    <x-forms.input name="birth_location" x-bind:required="participant == 0">{{ __('general.geburtsort') }}</x-forms.input>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <x-forms.input name="training_date" type="date">{{ __('general.datum_ausbildung') }}</x-forms.input>
                </div>
                <div class="col">
                    <x-forms.select.qualifications />
                </div>
            </div>
        </div>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.zertifikat_erstellen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
