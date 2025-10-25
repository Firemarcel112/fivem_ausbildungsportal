@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.voraussetzung_bearbeiten') }}
                </h2>
            </div>
        </div>
        @include('default.alerts')
        <x-card>
            <x-slot:header>
                <h2 class="card-title">{{ $requirement->getName() }} / {{ $requirement->fraction->getName() }} / {{ $qualification->getName() }}</h2>
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('administration.requirements.update', [
                    'qualification' => $qualification,
                    'requirement' => $requirement,
                ]) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$requirement->getName()" name="name" required>{{ __('general.name') }}</x-forms.input>
                    <x-forms.select label="{{ __('general.fraktion') }}" name="fraction_id" required>
                        @foreach ($fractions as $fraction)
                            <option @selected(old('fraction_id', $requirement->fraction->getId()) == $fraction->getId()) value="{{ $fraction->getId() }}">{{ $fraction->getFullName() }}</option>
                        @endforeach
                    </x-forms.select>
                    <x-forms.input :default="$qualification->getRank()" name="rank">{{ __('general.rank') }}</x-forms.input>
                    <x-forms.button :classes="['mt-2']">{{ __('general.speichern') }}</x-forms.button>
                </form>
            </x-slot:body>
        </x-card>
    </div>
@endsection
