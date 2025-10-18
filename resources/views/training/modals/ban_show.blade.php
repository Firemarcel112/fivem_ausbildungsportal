<x-modal id="training-ban-reason-{{ $user->getId() }}">
    <x-slot:title>
        {{ __('general.ausbildungssperre_fuer', [
            'name' => $user->account->getFullName(),
        ]) }}
    </x-slot:title>
    <x-slot:body>
        <p>{{ __('general.ausgestellt_von', [
            'name' => $user->activeTrainingBan->getIssuerName(),
        ]) }}
        </p>
        <hr>
        <p>{{ __('general.grund') }}</p>
        <p>{!! nl2br($user->activeTrainingBan->getReason()) !!}</p>
        @can('training.ban.show_internal_reason')
            <hr>
            <p>{{ __('general.interne_notizen') }}</p>
            <p>{!! nl2br($user->activeTrainingBan->getInternalNote()) !!}</p>
        @endcan
    </x-slot:body>
    <x-slot:footer>
        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
    </x-slot:footer>
</x-modal>
