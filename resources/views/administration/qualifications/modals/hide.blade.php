<x-modal body_classes="text-center" form_action="{{ route('administration.qualifications.toggle_hide', [$qualification]) }}" id="qualification-{{ $qualification->getKey() }}-hide">

    <x-slot:title>{{ __('general.ausblenden') }}</x-slot:title>
    <x-slot:body>
        <div class="text-center">
            <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
        </div>
        <p class="text-warning">{!! __('general.qualifikation_sicher_ausblenden', [
            'br' => '<br/>',
            'name' => $qualification->name,
        ]) !!}</p>
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
        <button class="btn btn-warning" type="submit">{{ __('general.ausblenden') }}</button>
    </x-slot:footer>
</x-modal>
