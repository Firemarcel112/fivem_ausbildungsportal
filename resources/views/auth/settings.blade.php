@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle text-blue">
                    {{ __('general.einstellungen') }}
                </div>
                <h2 class="page-title">
                    {{ $user->getFullName() }}
                </h2>
            </div>
            @include('default.alerts')
        </div>

        <x-card>
            <x-slot:header>
                <h2>
                    {{ __('general.accountdaten_aendern') }}
                </h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('user.store', $user) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$user->getName()" name="username" required>
                        {{ __('general.benutzername') }}
                    </x-forms.input>

                    <x-forms.input name="password" type="password">
                        {{ __('general.passwort_aendern') }}
                    </x-forms.input>

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
