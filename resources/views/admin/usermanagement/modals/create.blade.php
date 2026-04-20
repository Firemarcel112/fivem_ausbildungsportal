<x-modal form_action="{{ route('usermanagement.store') }}" id="create-new-user">
    <x-slot:title>{{ __('general.benutzer_anlegen') }}</x-slot:title>
    <x-slot:body>
        <x-forms.input :floating="true" name="username" required>{{ __('general.benutzername') }}</x-forms.input>

        <div class="row">
            <div class="col">
                <x-forms.input name="first_name" required>{{ __('general.vorname') }}</x-forms.input>
            </div>
            <div class="col">
                <x-forms.input name="last_name" required>{{ __('general.nachname') }}</x-forms.input>
            </div>
            <div class="col">
                <x-forms.select.genders />
            </div>
        </div>
        <div class="row">
            <div class="col">
                <x-forms.input name="date_of_birth" required type="date">{{ __('general.geburtsdatum') }}</x-forms.input>
            </div>
            <div class="col">
                <x-forms.input name="birth_location" required>{{ __('general.geburtsort') }}</x-forms.input>
            </div>
        </div>

        <div>
            <x-forms.select label="{{ __('general.default_fraktion') }}" name="default_fraction" required>
                @foreach ($fractions as $fraction)
                    <option value="{{ $fraction->getKey() }}">{{ $fraction->full_name }}</option>
                @endforeach
            </x-forms.select>
        </div>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.benutzer_anlegen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
