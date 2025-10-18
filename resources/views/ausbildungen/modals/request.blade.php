<x-modal form_action="{{ route('trainings.request') }}" id="training-request">
    <x-slot:title>{{ __('general.ausbildungswunsch') }}</x-slot:title>
    <x-slot:body>
        <x-alert :static="true">
            {{ __('general.ausbildungswunsch_info_text') }}
        </x-alert>
        <x-forms.select label="{{ __('general.ausbildung') }}" name="qualification_id" required required>
            <option disabled selected value="">-- {{ __('general.ausbildung_auswaehlen') }} --</option>
            @foreach ($qualifications ?? [] as $qualification)
                <option value="{{ $qualification->getId() }}">{{ $qualification->getName() }}</option>
            @endforeach
        </x-forms.select>

        <x-forms.input :max="now()->addDays(14)->format('Y-m-d')" :min="now()->addDay()->format('Y-m-d')" name="date" required type="date">
            {{ __('general.datum') }}
        </x-forms.input>

        <x-forms.input name="time" required type="time">
            {{ __('general.uhrzeit') }}
        </x-forms.input>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <button class="btn btn-primary" type="submit">{{ __('general.einreichen') }}</button>
    </x-slot:footer>
</x-modal>
