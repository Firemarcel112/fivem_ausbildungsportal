<x-modal form_action="{{ route('qualifications.user.remove', $user->id) }}" id="remove-user-qualification-{{ $user->id }}">
    <x-slot:title>
        {{ __('general.qualifikation_entfernen', [
            'name' => $user->full_name,
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <x-forms.select label="{{ __('general.qualifikation') }}" multiple name="qualifications" required>
            @foreach ($qualifications as $qualification)
                <option value="{{ $qualification->id }}">{{ $qualification->label }}</option>
            @endforeach
        </x-forms.select>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
