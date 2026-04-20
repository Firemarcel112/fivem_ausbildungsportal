@props(['id', 'full_name', 'issuer_name', 'reason', 'internal_note' => null])
<x-modal id="training-ban-reason-{{ $id }}">
    <x-slot:title>
        {{ __('general.ausbildungssperre_fuer', [
            'name' => $full_name,
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <p>{{ __('general.ausgestellt_von', [
            'name' => $issuer_name,
        ]) }}
        </p>
        <hr>
        <p>{{ __('general.grund') }}</p>
        <p>{!! $reason !!}</p>
        @can('training.ban.show_internal_reason')
            <hr>
            <p>{{ __('general.interne_notizen') }}</p>
            <p>{!! $internal_note !!}</p>
        @endcan
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
    </x-slot:footer>
</x-modal>
