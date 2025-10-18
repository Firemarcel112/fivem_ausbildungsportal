<x-modal form_action="{{ route('admin.roles.store') }}" id="create-new-role">
    <x-slot:title>{{ __('general.rolle_anlegen') }}</x-slot:title>
    <x-slot:body>

        <x-forms.input name="name" required>{{ __('general.name') }}</x-forms.input>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.rolle_anlegen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
