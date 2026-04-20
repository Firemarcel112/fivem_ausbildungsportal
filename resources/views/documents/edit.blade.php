@use('Illuminate\Support\Collection')
@use('App\DTO\SimpleListItem')
@use('App\DTO\DocumentViewData')
@php
    /** @var DocumentViewData $document **/
    /** @var Collection<int, SimpleListItem> $accounts **/
@endphp
@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class=" mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.dokumente') }}
                </div>
                <h2 class="page-title">
                    {{ $document->title }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
            @include('default.alerts')
        </div>
        <x-card>
            <x-slot:header>
                {{ __('general.bearbeiten') }}
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('documents.update', $document->id) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$document->title" name="title" required>
                        {{ __('general.name') }}
                    </x-forms.input>

                    <x-forms.select :label="__('general.zugeordnet_zu')" name="assign_info">
                        <option @selected(empty($document->assign_to_id)) value="">{{ __('general.nicht_zugeordnet') }}</option>
                        @foreach ($accounts as $account)
                            <option @selected($account->extra['selected']) value="{{ $account->id }}">{{ $account->label }} {{ $account->description }}</option>
                        @endforeach
                    </x-forms.select>

                    <div class="row">
                        <div class="col text-end">
                            <x-forms.button>
                                {{ __('general.speichern') }}
                            </x-forms.button>
                        </div>
                    </div>
                </form>
            </x-slot:body>
        </x-card>

    </div>
@endsection
