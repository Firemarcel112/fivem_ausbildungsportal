@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.qualifikation') }} / {{ $qualification->getName() }}
                </h2>
            </div>
        </div>
        @include('default.alerts')
        <x-card>
            <x-slot:header>
                <h2 class="card-title">{{ __('general.bearbeiten') }}</h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('administration.qualifications.update', $qualification) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$qualification->getName()" name="name" required>{{ __('general.name') }}</x-forms.input>
                    <x-forms.input :default="$qualification->getRank()" name="rank">{{ __('general.rank') }}</x-forms.input>
                    <x-forms.select label="{{ __('general.zertifikat_automatisch_erstellen') }}" name="generate_certificate" required>
                        <option @selected(old('generate_certificate', $qualification->getGenerateCertificate()) == 1) value="1">
                            {{ __('general.ja') }}
                        </option>
                        <option @selected(old('generate_certificate', $qualification->getGenerateCertificate()) == 0) value="0">
                            {{ __('general.nein') }}
                        </option>
                    </x-forms.select>
                    <x-forms.button :classes="['mt-2']">{{ __('general.speichern') }}</x-forms.button>
                </form>
            </x-slot:body>
        </x-card>
    </div>
@endsection
