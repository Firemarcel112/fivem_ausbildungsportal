<x-modal form_action="{{ route('administration.qualifications.store') }}" id="create-new-qualification">
    <x-slot:title>{{ __('general.qualifikation_anlegen') }}</x-slot:title>
    <x-slot:body>

        <x-forms.input name="name" required>{{ __('general.name') }}</x-forms.input>
        <x-forms.input name="rank" value="0">{{ __('general.rank') }}</x-forms.input>
        <x-forms.select label="{{ __('general.zertifikat_automatisch_erstellen') }}" name="generate_certificate" required>
            <option @selected(old('generate_certificate', 1) == 1) value="1">
                {{ __('general.ja') }}
            </option>
            <option @selected(old('generate_certificate', 1) == 0) value="0">
                {{ __('general.nein') }}
            </option>
        </x-forms.select>

    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.qualifikation_anlegen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
