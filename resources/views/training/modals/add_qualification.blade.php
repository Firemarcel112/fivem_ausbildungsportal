<x-modal form_action="{{ route('qualifications.user.assign', $user->id) }}" id="add-user-qualification-{{ $user->id }}">
    <x-slot:title>
        {{ __('general.qualifikation_zuweisen', [
            'name' => $user->full_name,
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <x-forms.input name="date" required type="date">
            {{ __('general.datum') }}
        </x-forms.input>

        <x-forms.select label="{{ __('general.qualifikation') }}" name="qualification_id" required>
            @foreach ($qualifications as $qualification)
                @continue(!empty($user->qualifications->firstWhere('id', $qualification->getKey())))
                <option value="{{ $qualification->getKey() }}">{{ $qualification->name }}</option>
            @endforeach
        </x-forms.select>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
