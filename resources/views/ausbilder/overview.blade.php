@use('Illuminate\Support\Collection')
@use('App\DTO\TrainingViewData')
@php
    /** @var Collection<int, TrainingViewData> $trainings **/
@endphp
@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbilder') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.dashboard') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <x-trainings.modal.create />
                    @can('announcements')
                        <a class="btn btn-primary" data-bs-target="#ankuendigung_erstellen" data-bs-toggle="modal">
                            {{ __('general.ankuendigung_erstellen') }}
                        </a>
                        @include('ausbildungen.modals.announcement')
                    @endcan
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
                            <th>{{ __('general.qualifikation') }}</th>
                            <th>{{ __('general.ausbilder') }}</th>
                            <th>{{ __('general.treffpunkt') }}</th>
                            <th>{{ __('general.datum_uhrzeit') }}</th>
                            <th>{{ __('general.teilnehmeranzahl') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                            <tr>
                                <td>{{ $training->id }}</td>
                                <td>{{ $training->name }}</td>
                                <td>{{ $training->trainer_name }}</td>
                                <td>{{ $training->meeting_point }}</td>
                                <td>{{ $training->full_time }}</td>
                                <td>{{ $training->count_participants }} / {{ $training->max_participants }}</td>
                                <td class="text-end">
                                    <a class="me-2 btn btn-sm btn-info" href="{{ route('ausbildung.show', $training->id) }}">
                                        <x-icon :hovertext="__('general.ansehen')" name="eye" />
                                    </a>
                                    @can('delete', $training)
                                        <button class="cursor-pointer btn btn-sm btn-danger" data-bs-target="#ausbildung-{{ $training->id }}-loeschen" data-bs-toggle="modal">
                                            <x-icon name="trash" />
                                        </button>
                                    @endcan
                                </td>
                            </tr>

                            @if ($training->can_delete)
                                <x-modal body_classes="text-center" form_action="{{ route('ausbildung.delete', [$training->id]) }}" id="ausbildung-{{ $training->id }}-loeschen">

                                    <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
                                    <x-slot:body>
                                        <div class="text-center">
                                            <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
                                        </div>
                                        <p class="text-warning">{!! __('general.ausbildung_loeschen_confirm', [
                                            'br' => '<br/>',
                                        ]) !!}</p>
                                    </x-slot:body>
                                    <x-slot:footer>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
                                        <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
                                    </x-slot:footer>
                                </x-modal>
                            @endif

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
