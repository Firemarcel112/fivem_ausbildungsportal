<x-modal form_action="{{ route('administration.requirements.store', $qualification) }}" id="create-new-requirement">
    <x-slot:title>{{ __('general.voraussetzung_anlegen') }}</x-slot:title>
    <x-slot:body>

        <x-forms.input name="name" required>{{ __('general.name') }}</x-forms.input>
        <x-forms.select label="{{ __('general.fraktion') }}" name="fraction_id" required>
            @foreach ($fractions as $fraction)
                <option value="{{ $fraction->getId() }}">{{ $fraction->getFullName() }}</option>
            @endforeach
        </x-forms.select>
        <x-forms.input name="rank" value="0">{{ __('general.rank') }}</x-forms.input>

    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.voraussetzung_anlegen') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
