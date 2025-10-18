<x-modal form_action="{{ route('trainings.ban', $user) }}" id="add-training-ban-{{ $user->getId() }}">
    <x-slot:title>
        {{ __('general.ausbildungssperre_fuer', [
            'name' => $user->getFullName(),
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <x-forms.input name="date_from" required type="date" value="{{ date('Y-m-d') }}">
            {{ __('general.datum_von') }}
        </x-forms.input>

        <x-forms.input name="date_to" required type="date" value="{{ now()->addDays(7)->format('Y-m-d') }}">
            {{ __('general.datum_bis') }}
        </x-forms.input>

        <div class="form-floating">
            <textarea class="form-control" id="reason" name="reason" required style="height:100px"></textarea>
            <label class="form-label required" for="reason">{{ __('general.grund') }}</label>
        </div>

        <div class="form-floating">
            <textarea class="form-control" id="reason" name="notice" style="height:100px"></textarea>
            <label class="form-label">{{ __('general.interne_notizen') }}</label>
        </div>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <x-forms.button>{{ __('general.speichern') }}</x-forms.button>
    </x-slot:footer>
</x-modal>
