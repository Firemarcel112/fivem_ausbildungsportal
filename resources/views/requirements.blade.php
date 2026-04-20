@use('Illuminate\Support\Collection')
@use('App\DTO\QualificationRequirementViewData')
@php
    /** @var Collection<string, Collection<string, Collection<int, QualificationRequirementViewData>>> $requirements **/
@endphp

@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="row mb-2 g-2 align-items-center">
            @include('default.alerts')

            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungen') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.voraussetzungen') }}
                </h2>
            </div>
        </div>
        <div class="mt-2 row">
            <x-alert :static="true" type="info">
                {{ __('general.voraussetzungen_erfragbar') }}
            </x-alert>

            @forelse($requirements as $qualification_name => $items)
                <x-card :classes="['mb-2']" size="sm">
                    <x-slot:header>
                        <h2 class="card-title">{{ $qualification_name }}</h2>
                    </x-slot:header>
                    <x-slot:body>
                        @foreach ($items as $fraction_name => $requirement_items)
                            @php
                                $unique_id = $requirement_items->first()->unique_id;
                            @endphp
                            <div class="accordion" id="fractions-accordion-{{ $unique_id }}">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <button aria-controls="collapse-fractions-{{ $unique_id }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-fractions-{{ $unique_id }}" data-bs-toggle="collapse">
                                            {{ $fraction_name }}
                                            <div class="accordion-button-toggle">
                                                <x-icon name="chevron-down" />
                                            </div>
                                        </button>

                                    </div>
                                    <div class="accordion-collapse collapse" data-bs-parent="#fractions-accordion-{{ $unique_id }}" id="collapse-fractions-{{ $unique_id }}">
                                        <div class="accordion-body">
                                            <ul style="list-style: none; padding-left: 0;">
                                                @foreach ($requirement_items as $requirement_item)
                                                    <li class="mt-2">{{ $requirement_item->label }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-slot:body>
                </x-card>
            @empty
                <x-alert :static="true" type="warning">
                    {{ __('general.voraussetzungen_nicht_verfuegbar') }}
                </x-alert>
            @endforelse
        </div>
    </div>
@endsection
