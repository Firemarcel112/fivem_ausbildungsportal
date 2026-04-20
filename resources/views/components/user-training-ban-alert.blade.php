@php
    /** @var array<string, string> $training_ban **/
@endphp
<x-alert :static="true" type="error">
    <div class="cursor-pointer" data-bs-target="#training_ban_reason_modal" data-bs-toggle="modal">
        {{ __('general.ausbildungssperre_bis', [
            'date_from' => $training_ban['date_from'],
            'date_to' => $training_ban['date_to'],
        ]) }}
        -
        {{ __('general.ausgestellt_von', [
            'name' => $training_ban['issuer'],
        ]) }}
        <br>
        <a>{{ __('general.fuer_begruendung_klicken') }}</span>
    </div>
</x-alert>
<x-modal id="training_ban_reason_modal">
    <x-slot:title>
        {{ __('general.ausbildungssperre_bis', [
            'date_from' => $training_ban['date_from'],
            'date_to' => $training_ban['date_to'],
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <p>
            {{ __('general.ausgestellt_von', [
                'name' => $training_ban['issuer'],
            ]) }}
        </p>
        <p>{{ __('general.grund') }}</p>
        {{ $training_ban['reason'] }}
    </x-slot:body>
</x-modal>
