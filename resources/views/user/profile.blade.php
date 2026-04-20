@use('App\DTO\UserProfileViewData')
@php
    /** @var UserProfileViewData $profile **/
@endphp
@extends('layouts.app')
@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.profil') }}
                </div>
                <div class="page-title">
                    <h2>{{ $profile->salutation }} {{ $profile->full_name }}
                </div>
            </div>
            @include('default.alerts')
        </div>

        <x-card :classes="['mb-3']" size="default">
            <x-slot:body>
                <div class="d-flex">
                    <x-avatar :initials="$profile->initials" size="lg" />
                    <div class="ms-2">
						<span class="d-block">{{ __('general.benutzer_id') }}: {{ $profile->id }}</span>
                        <span class="d-block">{{ $profile->salutation }} {{ $profile->full_name }}</span>
                        <span class="d-block">{{ $profile->date_of_birth }}</span>
                        <span class="d-block">{{ $profile->birth_location }}</span>
                    </div>
                </div>
            </x-slot:body>
        </x-card>

        <x-card size="default">
            <x-slot:header>
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a aria-selected="false" class="nav-link active" data-bs-toggle="tab" href="#tabs-certificates" role="tab" tabindex="-1">
                            <x-icon name="file-certificate" /><span class="ms-1">{{ __('general.zertifikate') }}</span>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-selected="false" class="nav-link" data-bs-toggle="tab" href="#tabs-qualifications" role="tab" tabindex="-1">
                            <x-icon name="rosette-discount-check" /><span class="ms-1">{{ __('general.qualifikationen') }}</span>
                        </a>
                    </li>
                </ul>
            </x-slot:header>
            <x-slot:body>
                <div class="tab-content">
                    <div class="tab-pane active show" id="tabs-certificates" role="tabpanel">
                        <ul>
                            @forelse ($profile->certificates as $document)
                                <li class="mb-2">
                                    <a class="d-inline-flex items-center gap-1" href="{{ route('documents.show', $document->id) }}" target="_blank">
                                        <x-icon :width-height="18" name="download" />
                                        {{ $document->label }}
                                        ({{ __('general.dokument_erstellt_am') }} {{ $document->description }})
                                    </a>
                                </li>
                            @empty
                                <li>
                                    {{ __('general.keine_zertifikate') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>

                    <div class="tab-pane" id="tabs-qualifications" role="tabpanel">
                        <div>
                            <ul>
                                @forelse ($profile->qualifications as $qualification)
                                    <li>
                                        {{ $qualification->label }}
                                        ({{ $qualification->description }})
                                    </li>
                                @empty
                                    <li>
                                        {{ __('general.keine_qualifikation') }}
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </x-slot:body>
        </x-card>
    </div>
@endsection
