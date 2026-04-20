@use('Illuminate\Support\Collection')
@use('App\DTO\TrainingRequestViewData')
@php
    /** @var Collection<string, Collection<string, Collection<TrainingRequestViewData>>> $data **/
@endphp
@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungswuensche') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
        </div>
        @include('default.alerts')

        <div class="row">
            @foreach ($data as $date => $items)
                <div class="col-4">
                    <h2>{{ $date }}</h2>
                    <x-card card-body-classes="m-0 p-2">
                        <x-slot:body>
                            <div class="accordion accordion-flush" id="accordion-flush">
                                @foreach ($items as $name => $view_data)
                                    <x-accordion-item>
                                        <x-slot:header>{{ $name }} ({{ __('general.fruehstens') }} : {{ $view_data->first()->time }} {{ __('general.uhr') }})</x-slot:header>
                                        <x-slot:body>
                                            <ul>
                                                @foreach ($view_data as $item)
                                                    @if ($loop->first)
                                                        <p class="mb-1">{{ __('general.angefordert_von') }}:</p>
                                                    @endif
                                                    <li>{{ $item->requested_user->label }} {{ $item->requested_user->description }} {{ __('general.ab') }} {{ $item->time }} {{ __('general.uhr') }}</li>
                                                @endforeach
                                            </ul>
                                        </x-slot:body>
                                    </x-accordion-item>
                                @endforeach
                            </div>
                        </x-slot:body>
                    </x-card>
                </div>
            @endforeach
        </div>
    </div>
@endsection
