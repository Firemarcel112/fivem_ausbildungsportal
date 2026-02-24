@extends('layouts.app')

@section('content')
    <div class="mb-2 container-md-xl container-sm">
        <div class="mt-4 row justify-content-center">
            <div class="col-12 col-md-8">
                @include('default.alerts')
            </div>
        </div>
        <div class="mt-2 mb-4 row justify-content-center">
            <div class="col-12 col-md-8">
                @if (config('app.registration_allowed'))
                    <x-card :classes="['mx-auto']">
                        <x-slot:header>
                            <h5 class="card-title">{{ __('general.registrieren') }}</h5>
                        </x-slot:header>
                        <x-slot:body>
                            <form action="{{ route('register.store') }}" class="space-y" method="POST">
                                @csrf
                                <div>
                                    <h4>{{ __('general.accountdaten') }}</h4>
                                </div>

                                <div>
                                    <x-forms.input name="username" required>{{ __('general.benutzername') }}</x-forms.input>
                                </div>
                                <div>
                                    <x-forms.input name="password" required type="password">{{ __('general.passwort') }}</x-forms.input>
                                </div>

                                <div>
                                    <h4>{{ __('general.rp_charakter') }}</h4>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <x-forms.input name="first_name" required>{{ __('general.vorname') }}</x-forms.input>
                                    </div>
                                    <div class="col">
                                        <x-forms.input name="last_name" required>{{ __('general.nachname') }}</x-forms.input>
                                    </div>
                                    <div class="col">
                                        <x-forms.select label="{{ __('general.geschlecht') }}" name="gender" required>
                                            @foreach ($genders as $gender)
                                                <option value="{{ $gender['value'] }}">{{ $gender['name'] }}</option>
                                            @endforeach
                                        </x-forms.select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <x-forms.input name="birth_location" required>{{ __('general.geburtsort') }}</x-forms.input>
                                    </div>
                                    <div class="col">
                                        <x-forms.input name="date_of_birth" required type="date">{{ __('general.geburtsdatum') }}</x-forms.input>
                                    </div>
                                </div>

                                <div>
                                    <x-forms.select label="{{ __('general.default_fraktion') }}" name="default_fraction" required>
                                        @foreach ($fractions as $fraction)
                                            <option value="{{ $fraction->getKey() }}">{{ $fraction->full_name }}</option>
                                        @endforeach
                                    </x-forms.select>
                                </div>
                                <div>
                                    <x-forms.select label="{{ __('general.fraktion') }}" multiple name="fraction">
                                        @foreach ($fractions as $fraction)
                                            <option value="{{ $fraction->getKey() }}">{{ $fraction->full_name }}</option>
                                        @endforeach
                                    </x-forms.select>
                                </div>


                                <div class="row">
                                    <div class="col text-end">
                                        <x-forms.button>
                                            {{ __('general.registrieren') }}
                                        </x-forms.button>
                                    </div>
                                </div>
                            </form>
                        </x-slot:body>
                    </x-card>
                @else
                    <x-alert :static="true" type="danger">{{ __('general.registrierung_geschlossen') }}</x-alert>
                @endif
            </div>
        </div>
    </div>
@endsection
