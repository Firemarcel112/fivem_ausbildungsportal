@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.fraktionen') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary" data-bs-target="#create-new-fraction" data-bs-toggle="modal">
                        <x-icon :classes="['me-2']" name="plus" />{{ __('general.fraktion_anlegen') }}
                    </a>
                    @include('administration.fractions.modals.create')
                </div>
            </div>
        </div>
        @include('default.alerts')
        <div class="mt-2 row">
            <div class="table-responsive">
                <table class="table table-vcenter table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.kuerzel') }}</th>
                            <th>{{ __('general.discord_webhook') }}</th>
                            <th>{{ __('general.discord_webhook_abschluss') }}</th>
                            <th>
                                <span>{{ __('general.hauptfraktion') }}</span>
                                <x-icon :hovertext="__('texte.administration.erklaerung_hauptfraktion')" name="info-circle" />
                            </th>
                            <th></th>
                        </tr>
                        @foreach ($fractions as $fraction)
                            <tr>
                                <td>
                                    {{ $fraction->getId() }}
                                </td>
                                <td>
                                    {{ $fraction->getName() }}
                                </td>
                                <td>
                                    {{ $fraction->getShortName() }}
                                </td>
                                <td data-bs-title="{{ $fraction->getDiscordWebhook() }}" data-bs-toggle="tooltip">
                                    <div class="text-truncate" style="max-width:200px">
                                        @if (empty($fraction->getDiscordWebhook()))
                                            <span class="text-muted">{{ __('general.nicht_festgelegt') }}</span>
                                        @else
                                            {{ $fraction->getDiscordWebhook() }}
                                        @endif
                                    </div>
                                </td>
                                <td data-bs-title="{{ $fraction->getDiscordWebhookCompleted() }}" data-bs-toggle="tooltip">
                                    <div class="text-truncate" style="max-width:200px">
                                        @if (empty($fraction->getDiscordWebhookCompleted()))
                                            <span class="text-muted">{{ __('general.nicht_festgelegt') }}</span>
                                        @else
                                            {{ $fraction->getDiscordWebhookCompleted() }}
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ $fraction->getMaster() ? __('general.ja') : __('general.nein') }}
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-primary btn-sm me-2" href="{{ route('administration.fractions.edit', $fraction) }}">
                                        <x-icon :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                    <a class="btn btn-danger btn-sm" data-bs-target="#fraction-{{ $fraction->getId() }}-delete" data-bs-toggle="modal">
                                        <x-icon :hovertext="__('general.loeschen')" name="trash" />
                                    </a>
                                </td>
                            </tr>
                            @include('administration.fractions.modals.delete', [
                                'fraction' => $fraction,
                            ])
                        @endforeach
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
