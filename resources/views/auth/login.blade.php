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
                @if (config('app.login_allowed'))
                    <x-card :classes="['mx-auto']">
                        <x-slot:header>
                            <h5 class="card-title">{{ __('general.anmelden') }}</h5>
                        </x-slot:header>
                        <x-slot:body>
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <x-forms.input :classes="['mb-2']" :floating="true" name="username" required>
                                    {{ __('general.benutzername') }}
                                </x-forms.input>
                                <x-forms.input :classes="['mb-2']" :floating="true" name="password" required type="password">{{ __('general.passwort') }}</x-forms.input>

                                <x-forms.checkbox :classes="['mt-4']" name="remember">{{ __('general.angemeldet_bleiben') }}</x-forms.checkbox>


                                <div class="d-flex justify-content-between">
                                    <x-forms.button :classes="['mt-2']">{{ __('general.einloggen') }}</x-forms.button>
                                    @if (!empty(config('services.discord.client_id')))
                                        <a class="btn btn-purple" href="{{ route('discord.auth') }}">
                                            {{ __('general.login_via_discord') }}
                                        </a>
                                    @endif
                                </div>
                            </form>
                        </x-slot:body>
                        <x-slot:footer>
                            <a class="text-center text-white" href="{{ route('register.index') }}">{{ __('general.noch_kein_account') }}</a>
                        </x-slot:footer>
                    </x-card>
                @else
                    <x-alert :static="true" type="danger">{{ __('general.anmeldung_geschlossen') }}</x-alert>
                @endif
            </div>
        </div>
    </div>
@endsection
