<x-modal form_action="{{ route('admin.settings.update', $setting) }}" id="{{ $id }}">
    <x-slot:title>{{ __('general.einstellung_bearbeiten') }}: {{ $setting->key }}</x-slot:title>
    <x-slot:body>
        <x-forms.input :default="$setting->value" name="value" required>{{ __('general.wert') }}</x-forms.input>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
