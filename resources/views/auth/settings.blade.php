@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.einstellungen') }}
                </div>
                <h2 class="page-title">
                    {{ $user->getSalutation() }} {{ $user->getFullName() }}
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
                <form action="{{ route('user.store', $user) }}" class="space-y" method="POST">
                    @csrf

                    <div>
                        <h4>{{ __('general.accountdaten') }}</h4>
                    </div>

                    <div>
                        <x-forms.input :default="$user->getName()" name="username" required>{{ __('general.benutzername') }}</x-forms.input>
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
                            <x-forms.input :default="$user->account->getFirstName()" name="first_name" required>{{ __('general.vorname') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.input :default="$user->account->getLastName()" name="last_name" required>{{ __('general.nachname') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.select label="{{ __('general.geschlecht') }}" name="gender" required>
                                @foreach ($genders as $gender)
                                    <option @selected($user->account->getGender() == $gender['value']) value="{{ $gender['value'] }}">{{ $gender['name'] }}</option>
                                @endforeach
                            </x-forms.select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <x-forms.input :default="$user->account->getBirthLocation()" name="birth_location" required>{{ __('general.geburtsort') }}</x-forms.input>
                        </div>
                        <div class="col">
                            <x-forms.input :default="$user->account->getDateOfBirth()->format('Y-m-d')" name="date_of_birth" required type="date">{{ __('general.geburtsdatum') }}</x-forms.input>
                        </div>
                    </div>

                    <div>
                        <x-forms.select label="{{ __('general.default_fraktion') }}" name="default_fraction" required>
                            @foreach ($fractions as $fraction)
                                <option @selected($user->account->getDefaultFractionId() == $fraction->getId()) value="{{ $fraction->getId() }}">{{ $fraction->getFullName() }}</option>
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
