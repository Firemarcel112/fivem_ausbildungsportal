<x-modal form_action="{{ route('qualifications.user.remove', $user) }}" id="remove-user-qualification-{{ $user->getId() }}">
    <x-slot:title>
        {{ __('general.qualifikation_entfernen', [
            'name' => $user->getFullName(),
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <x-forms.select label="{{ __('general.qualifikation') }}" multiple name="qualifications" required>
            @foreach ($qualifications as $qualification)
                <option value="{{ $qualification->getId() }}">{{ $qualification->getName() }}</option>
            @endforeach
        </x-forms.select>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
