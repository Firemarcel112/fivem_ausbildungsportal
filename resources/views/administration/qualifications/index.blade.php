@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.administration') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.qualifikationen') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a class="btn btn-primary" data-bs-target="#create-new-qualification" data-bs-toggle="modal">
                        <x-icon :classes="['me-2']" name="plus" />{{ __('general.qualifikation_anlegen') }}
                    </a>
                    @include('administration.qualifications.modals.create')
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
                            <th>{{ __('general.zertifikat_automatisch_erstellen') }}</th>
                            <th>{{ __('general.rank') }}</th>
                            <th>{{ __('general.ausgeblendet') }}</th>
                            <th></th>
                        </tr>
                        @foreach ($qualifications as $qualification)
                            <tr>
                                <td>
                                    {{ $qualification->getId() }}
                                </td>
                                <td>
                                    {{ $qualification->getName() }}
                                </td>
                                <td>
                                    @if ($qualification->getGenerateCertificate())
                                        {{ __('general.ja') }}
                                    @else
                                        {{ __('general.nein') }}
                                    @endif
                                </td>
                                <td>
                                    {{ $qualification->getRank() }}
                                </td>
                                <td>
                                    @if ($qualification->getHide())
                                        {{ __('general.ja') }}
                                    @else
                                        {{ __('general.nein') }}
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-primary btn-sm me-2" href="{{ route('administration.requirements.show', $qualification) }}">
                                        <x-icon :hovertext="__('general.voraussetzungen')" name="shield-question" />
                                    </a>
                                    <a class="btn btn-primary btn-sm me-2" href="{{ route('administration.qualifications.edit', $qualification) }}">
                                        <x-icon :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                    @if (empty($qualification->trainings->count()) && empty($qualification->userQualifications->count()))
                                        <a class="btn btn-danger btn-sm" data-bs-target="#qualification-{{ $qualification->getId() }}-delete" data-bs-toggle="modal">
                                            <x-icon :hovertext="__('general.loeschen')" name="trash" />
                                        </a>
                                    @else
                                        @if ($qualification->getHide() == 0)
                                            <a class="btn btn-warning btn-sm" data-bs-target="#qualification-{{ $qualification->getId() }}-hide" data-bs-toggle="modal">
                                                <x-icon :hovertext="__('general.ausblenden')" name="eye-off" />
                                            </a>
                                        @else
                                            <a class="btn btn-secondaryw btn-sm" data-bs-target="#qualification-{{ $qualification->getId() }}-show" data-bs-toggle="modal">
                                                <x-icon :hovertext="__('general.einblenden')" name="eye" />
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @if (empty($qualification->trainings->count()) && empty($qualification->userQualifications->count()))
                                @include('administration.qualifications.modals.delete', [
                                    'qualification' => $qualification,
                                ])
                            @else
                                @includeWhen($qualification->getHide() == 0, 'administration.qualifications.modals.hide')
                                @includeWhen($qualification->getHide() == 1, 'administration.qualifications.modals.show')
                            @endif
                        @endforeach
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
