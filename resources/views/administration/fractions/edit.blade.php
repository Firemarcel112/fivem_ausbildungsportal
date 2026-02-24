@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.fraktionen') }} / {{ $fraction->name }}
                </h2>
            </div>
        </div>
        @include('default.alerts')
        <x-card>
            <x-slot:header>
                <h2 class="card-title">{{ __('general.bearbeiten') }}</h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('administration.fractions.update', $fraction) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$fraction->name" name="name" required>{{ __('general.name') }}</x-forms.input>
                    <x-forms.input :default="$fraction->short_name" maxlength="5" name="short_name" required>{{ __('general.kuerzel') }} ({{ __('general.maximal_zeichen', ['count' => 5]) }})</x-forms.input>
                    <x-forms.input :default="$fraction->discord_webhook" name="discord_webhook">{{ __('general.discord_webhook') }}</x-forms.input>
                    <x-forms.input :default="$fraction->discord_webhook_completed" name="discord_webhook_completed">{{ __('general.discord_webhook_abschluss') }}</x-forms.input>
                    <x-forms.checkbox :value="$fraction->master" name="master">
                        {{ __('general.hauptfraktion') }}
                        <x-icon :hovertext="__('texte.administration.erklaerung_hauptfraktion')" name="info-circle" />
                    </x-forms.checkbox>
                    <x-forms.button :classes="['mt-2']">{{ __('general.speichern') }}</x-forms.button>
                </form>
            </x-slot:body>
        </x-card>
    </div>
@endsection
