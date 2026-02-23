@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class=" mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.dokumente') }}
                </div>
                <h2 class="page-title">
                    {{ $document->title }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                </div>
            </div>
            @include('default.alerts')
        </div>
        <x-card>
            <x-slot:header>
                {{ __('general.bearbeiten') }}
            </x-slot:header>
            <x-slot:body>
                <form action="{{ route('documents.update', $document) }}" class="space-y" method="POST">
                    @csrf
                    <x-forms.input :default="$document->title" name="title" required>
                        {{ __('general.name') }}
                    </x-forms.input>

                    <x-forms.select :label="__('general.zugeordnet_zu')" name="assign">
                        @foreach ($users as $user)
                            <option @selected(!empty($document->documentAssign) && $document->documentAssign->link_id == $user->getKey()) value="{{ $user->getKey() }}">{{ $user->getSalutation() }} {{ $user->getFullName() }} ({{ $user->getId() }})</option>
                        @endforeach
                    </x-forms.select>

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
