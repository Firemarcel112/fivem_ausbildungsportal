@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.profil') }}
                </div>
                <div class="page-title">
                    <h2>{{ $user->getFullName() }}
                </div>
            </div>
            @include('default.alerts')
        </div>
        <x-card>
            <x-slot:header>
                <div class="row w-100">
                    <div class="col">
                        <div class="d-flex">
                            <x-avatar :user="$user" size="lg"></x-avatar>
                            <div class="ms-2">
                                <span class="d-block">{{ $user->getFullName() }}</span>
                                @empty(!$user->discord)
                                    <span class="d-block">
                                        {{ __('general.discord_name') }} ({{ config('app.primary_guild_name') }}): {{ $user->getDiscordName() }}
                                    </span>
                                @endempty
                                <span class="d-block">{{ $user->account->getDateOfBirth()->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end align-items-center">
                        @empty(!$user->discord)
                            <x-icon name="discord" />
                        @endempty
                    </div>
                </div>
            </x-slot:header>
            <x-slot:body>
                @if (!empty($user->account->qualifications?->first()))
                    <div>
                        <h3>{{ __('general.meine_qualifikationen') }}</h3>
                        <ul>
                            @foreach ($user->account->qualifications as $qualification)
                                <li>
                                    {{ $qualification->getName() }}
                                    ({{ now()->parse($qualification->pivot->created_at)->format('d.m.Y') }})
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </x-slot:body>
        </x-card>
    </div>
@endsection
