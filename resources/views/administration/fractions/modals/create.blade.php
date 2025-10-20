<x-modal form_action="{{ route('administration.fractions.store') }}" id="create-new-fraction">
    <x-slot:title>{{ __('general.fraktion_anlegen') }}</x-slot:title>
    <x-slot:body>

        <x-forms.input name="name" required>{{ __('general.name') }}</x-forms.input>
        <x-forms.input maxlength="5" name="short_name" required>{{ __('general.kuerzel') }} ({{ __('general.maximal_zeichen', ['count' => 5]) }})</x-forms.input>
        <x-forms.input name="discord_webhook">{{ __('general.discord_webhook') }}</x-forms.input>
        <x-forms.input name="discord_webhook_completed">{{ __('general.discord_webhook_abschluss') }}</x-forms.input>
        <x-forms.checkbox name="master">
            {{ __('general.hauptfraktion') }}
            <x-icon :hovertext="__('texte.administration.erklaerung_hauptfraktion')" name="info-circle" />
        </x-forms.checkbox>

    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.fraktion_anlegen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
