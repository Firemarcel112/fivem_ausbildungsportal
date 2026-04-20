@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.voraussetzungen') }}: {{ $qualification->name }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary" data-bs-target="#create-new-requirement" data-bs-toggle="modal">
                        <x-icon :classes="['me-2']" name="plus" />{{ __('general.voraussetzung_anlegen') }}
                    </a>
                    @include('administration.requirements.modals.create', [
                        'fractions' => $fractions,
                        'qualification' => $qualification,
                    ])
                </div>
            </div>
        </div>
        @include('default.alerts')
        <div class="mt-2 row">
            <div class="table-responsive">
                <table class="table table-vcenter table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.fraktion') }}</th>
                            <th>{{ __('general.rank') }}</th>
                            <th></th>
                        </tr>
                        @foreach ($requirements as $requirement)
                            <tr>
                                <td>
                                    {{ $requirement->getKey() }}
                                </td>
                                <td>
                                    {{ $requirement->name }}
                                </td>
                                <td>
                                    {{ $requirement->fraction->name }}
                                </td>
                                <td>
                                    {{ $requirement->rank }}
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-primary btn-sm me-2" href="{{ route('administration.requirements.edit', [
                                        'qualification' => $qualification,
                                        'requirement' => $requirement,
                                    ]) }}">
                                        <x-icon :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                    <a class="btn btn-danger btn-sm" data-bs-target="#requirement-{{ $requirement->getKey() }}-delete" data-bs-toggle="modal">
                                        <x-icon :hovertext="__('general.loeschen')" name="trash" />
                                    </a>
                                </td>
                            </tr>
                            @include('administration.requirements.modals.delete', [
                                'requirement' => $requirement,
                                'qualification' => $qualification,
                            ])
                        @endforeach
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
