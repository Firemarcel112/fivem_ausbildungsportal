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
            @forelse($qualifications as $qualification)
                @continue(($qualification->requirements?->count() ?? 0) == 0)
                <x-card :classes="['mb-2']" size="sm">
                    <x-slot:header>
                        <h2 class="card-title">{{ $qualification->getName() }}</h2>
                    </x-slot:header>
                    <x-slot:body>
                        @foreach ($fractions as $fraction)
                            @continue(empty($qualification->requirements->firstWhere('fraction_id', $fraction->fraction_id)))
                            <div class="accordion" id="fractions-accordion-{{ $fraction->getKey() }}-{{ $qualification->getId() }}">
                                <div class="accordion-item">
                                    <div class="accordion-header">
                                        <button aria-controls="collapse-fractions-{{ $fraction->getKey() }}-{{ $qualification->getId() }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-fractions-{{ $fraction->getKey() }}-{{ $qualification->getId() }}" data-bs-toggle="collapse">
                                            {{ $fraction->name }}
                                            <div class="accordion-button-toggle">
                                                <x-icon name="chevron-down" />
                                            </div>
                                        </button>

                                    </div>
                                    <div class="accordion-collapse collapse" data-bs-parent="#fractions-accordion-{{ $fraction->getKey() }}-{{ $qualification->getId() }}" id="collapse-fractions-{{ $fraction->getKey() }}-{{ $qualification->getId() }}">
                                        <div class="accordion-body">
                                            <ul style="list-style: none; padding-left: 0;">
                                                @foreach ($qualification->requirements as $requirement)
                                                    @continue($requirement->getFractionId() != $fraction->fraction_id)
                                                    <li class="mt-2">{{ $requirement->getName() }}</li>
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
