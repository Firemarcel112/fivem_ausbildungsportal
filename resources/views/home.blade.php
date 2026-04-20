@php
    /** @var array<string, \App\DTO\TrainingGroupDTO> $trainings */
@endphp
@props([
    'trainings' => [],
])
@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="row mb-2 g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungen') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list d-flex flex-column flex-md-row">
                    @auth
                        <a class="mb-2 btn btn-primary mb-md-0 me-md-2" data-bs-target="#training-request" data-bs-toggle="modal">
                            <span>{{ __('general.ausbildungswunsch') }}</span>
                        </a>
                        @include('ausbildungen.modals.request', [
                            'id' => 'training-request',
                        ])
                    @endauth

                    @can('trainings.store')
                        <div class="input-group w-auto">
                            <x-trainings.modal.create />
                            @can('announcements')
                                @include('ausbildungen.modals.announcement')
                                <div class="btn btn-primary dropdown-toggle dropdown-toggle-spli" data-bs-toggle="dropdown"></div>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <a class="dropdown-item" data-bs-target="#ankuendigung_erstellen" data-bs-toggle="modal" href="javascript::void(0)">{{ __('general.ankuendigung_erstellen') }}</a>
                                </div>
                            @endcan
                        </div>
                    @endcan
                </div>
            </div>
            @include('default.alerts')

            <x-user-training-ban-alert />

        </div>

        <div class="row">
            @forelse($trainings ?? [] as $group)
                <div class="col-12">
                    <h2 class="fw-bold">{{ $group->date_label }}</h2>
                </div>
                <div class="row g-2 mb-2">
                    @foreach ($group->items as $item)
                        <div class="col-md-4 col-12 h-auto">
                            <x-training-card :training="$item" />
                        </div>
                    @endforeach
                </div>
            @empty
                <x-alert :static="true" type="warning">{{ __('general.aktuell_keine_ausbildungen') }}</x-alert>
            @endforelse
        </div>
    </div>
@endsection
