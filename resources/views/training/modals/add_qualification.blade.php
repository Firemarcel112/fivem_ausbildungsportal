<x-modal form_action="{{ route('qualifications.user.assign', $user) }}" id="add-user-qualification-{{ $user->getId() }}">
    <x-slot:title>
        {{ __('general.qualifikation_zuweisen', [
            'name' => $user->getFullName(),
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <x-forms.input name="date" required type="date">
            {{ __('general.datum') }}
        </x-forms.input>

        <x-forms.select label="{{ __('general.qualifikation') }}" name="qualification_id" required>
            @foreach ($qualifications as $qualification)
                @continue($user->account->qualifications->firstWhere('qualification_id', $qualification->getId())?->exists)
                <option value="{{ $qualification->getId() }}">{{ $qualification->getName() }}</option>
            @endforeach
        </x-forms.select>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
