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
                    {{ __('general.abgeschlossene_ausbildungen') }}
                </h2>
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
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <x-paginate :data="$trainings" />
            </div>
        </div>
    </div>
@endsection
