@props([
    'id' => 0,
    'name' => '',
])
@can('documents.delete')
    <x-modal body_classes="text-center" form_action="{{ route('documents.destroy', [$id]) }}" id="document-{{ $id }}-delete">

        <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
        <x-slot:body>
            <div class="text-center">
                <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
            </div>
            <p class="text-warning">{!! __('general.dokument_sicher_loeschen', [
                'br' => '<br/>',
                'name' => $name,
            ]) !!}</p>
        </x-slot:body>
        <x-slot:footer>
            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
            <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
        </x-slot:footer>
    </x-modal>
@endcan
