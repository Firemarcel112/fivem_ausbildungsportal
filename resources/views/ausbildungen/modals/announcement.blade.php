<x-modal form_action="{{ route('announcement.store') }}" id="ankuendigung_erstellen">
    <x-slot:title>{{ __('general.ankuendigung_erstellen') }}</x-slot:title>
    <x-slot:body>
        <x-forms.input name="title">
            {{ __('general.titel') }} ({{ __('general.optional') }})
        </x-forms.input>
        <div class="form-floating">
            <textarea class="form-control" id="text" name="text" placeholder="Nachricht" required style="height: 100px">{{ old('text') }}</textarea>
            <label for="text">{{ __('general.nachricht') }}</label>
        </div>
        <x-forms.input name="roles">
            Rollen Erwähnen (Mit Rollen IDs, mehrere mit "," getrennt) ({{ __('general.optional') }})
        </x-forms.input>

        <div>
            <x-discord_notification />
        </div>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <button class="btn btn-primary" type="submit">{{ __('general.senden') }}</button>
    </x-slot:footer>
</x-modal>
