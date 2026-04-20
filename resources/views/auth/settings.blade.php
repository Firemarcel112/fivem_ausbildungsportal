@use('App\DTO\SimpleUserViewData')
@use('App\DTO\SimpleListItem')
@use('Illuminate\Support\Collection')
@php
    /** @var SimpleUserViewData $user **/
    /** @var Collection<SimpleListItem> $fractions **/
@endphp

@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.einstellungen') }}
                </div>
                <h2 class="page-title">
                    {{ $user->salutation }} {{ $user->full_name }}
                </h2>
            </div>
            @include('default.alerts')
        </div>

        <x-card>
            <x-slot:header>
                <h2>
                    {{ __('general.daten_aendern') }}
                </h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('user.update', $user->id) }}" class="space-y" method="POST">
                    @csrf

                    <div>
                        <h4>{{ __('general.accountdaten') }}</h4>
                    </div>

                    <div>
                        <x-forms.input :default="$user->username" name="username" required>{{ __('general.benutzername') }}</x-forms.input>
                    </div>
                    <div>
                        <x-forms.input name="password" type="password">
                            {{ __('general.passwort_aendern') }}
                        </x-forms.input>
                    </div>

                    <div>
                        <h4>{{ __('general.rp_charakter') }}</h4>
                    </div>
                    <div class="row">
                        <div class="col">
                            <x-forms.input :default="$user->first_name" name="first_name" required>{{ __('general.vorname') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.input :default="$user->last_name" name="last_name" required>{{ __('general.nachname') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.select.genders />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <x-forms.input :default="$user->birth_location" name="birth_location" required>{{ __('general.geburtsort') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.input :default="$user->date_of_birth->format('Y-m-d')" name="date_of_birth" required type="date">{{ __('general.geburtsdatum') }}</x-forms.input>
                        </div>
                    </div>

                    <div>
                        <x-forms.select label="{{ __('general.default_fraktion') }}" name="default_fraction" required>
                            @foreach ($fractions as $fraction)
                                <option @selected($user->fraction_data['default']['id'] == $fraction->id) value="{{ $fraction->id }}">{{ $fraction->label }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

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
