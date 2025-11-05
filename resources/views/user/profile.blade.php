@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.profil') }}
                </div>
                <div class="page-title">
                    <h2>{{ auth()->user()->getSalutation() }} {{ $user->getFullName() }}
                </div>
            </div>
            @include('default.alerts')
        </div>

        <x-card size="default">
            <x-slot:header>
                <ul class="nav nav-tabs card-header-tabs" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a aria-selected="true" class="nav-link active" data-bs-toggle="tab" href="#tabs-profile" role="tab">{{ __('general.profil') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-selected="false" class="nav-link" data-bs-toggle="tab" href="#tabs-qualifications" role="tab" tabindex="-1">{{ __('general.qualifikation') }}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a aria-selected="false" class="nav-link" data-bs-toggle="tab" href="#tabs-certificates" role="tab" tabindex="-1">{{ __('general.zertifikate') }}</a>
                    </li>
                </ul>
            </x-slot:header>
            <x-slot:body>
                <div class="tab-content">
                    <div class="tab-pane active show" id="tabs-profile" role="tabpanel">
                        <h3>{{ __('general.profil') }}</h3>
                        <div class="d-flex">
                            <x-avatar :user="$user" size="lg"></x-avatar>
                            <div class="ms-2">
                                <span class="d-block">{{ auth()->user()->getSalutation() }} {{ $user->getFullName() }}</span>
                                @if (!empty($user->discord) && !empty(config('services.discord.client_id')))
                                    <span class="d-block">
                                        {{ __('general.discord_name') }} ({{ config('app.primary_guild_name') }}): {{ $user->getDiscordName() }}
                                    </span>
                                @endif
                                <span class="d-block">{{ $user->account->getDateOfBirth()->format('d.m.Y') }}</span>
                                <span class="d-block">{{ $user->account?->getBirthLocation() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabs-qualifications" role="tabpanel">
                        <h3>{{ __('general.meine_qualifikationen') }}</h3>
                        <div>
                            <ul>
                                @forelse ($user->account->qualifications as $qualification)
                                    <li>
                                        {{ $qualification->getName() }}
                                        ({{ now()->parse($qualification->pivot->created_at)->format('d.m.Y') }})
                                    </li>
                                @empty
                                    <li>
                                        {{ __('general.keine_qualifikation') }}
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="tab-pane" id="tabs-certificates" role="tabpanel">
                        <h4>{{ __('general.meine_zertifikate') }}</h4>
                        <ul>
                            @forelse ($user->account->certificates as $document)
                                <li>
                                    <a href="{{ route('documents.show', $document) }}" target="_blank">
                                        {{ $document->getTitle() }}
                                        ({{ __('general.dokument_erstellt_am') }} {{ now()->parse($document->getCreated())->format('d.m.Y') }})
                                    </a>
                                </li>
                            @empty
                                <li>
                                    {{ __('general.keine_zertifikate') }}
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </x-slot:body>
        </x-card>
    </div>
@endsection
