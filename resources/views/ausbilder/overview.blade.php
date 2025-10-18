@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbilder') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.dashboard') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <x-trainings.modal.create />
                    @can('announcements')
                        <a class="btn btn-primary" data-bs-target="#ankuendigung_erstellen" data-bs-toggle="modal">
                            {{ __('general.ankuendigung_erstellen') }}
                        </a>
                        @include('ausbildungen.modals.announcement')
                    @endcan
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
                            <th>{{ __('general.qualifikation') }}</th>
                            <th>{{ __('general.ausbilder') }}</th>
                            <th>{{ __('general.treffpunkt') }}</th>
                            <th>{{ __('general.datum_uhrzeit') }}</th>
                            <th>{{ __('general.teilnehmeranzahl') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings as $training)
                            <tr>
                                <td>{{ $training->getId() }}</td>
                                <td>{{ $training->getName() }}</td>
                                <td>{{ $training->getTrainerName() }}</td>
                                <td>{{ $training->getMeetingPoint() }}</td>
                                <td>{{ $training->getFormatedDate() }}</td>
                                <td>{{ $training->getCountParticipants() }} / {{ $training->getMaxParticipants() }}</td>
                                <td class="text-end">
                                    <a class="me-2 btn btn-sm btn-info" href="{{ route('ausbildung.show', $training) }}">
                                        <x-icon :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                    @can('trainings.delete')
                                        <button class="cursor-pointer btn btn-sm btn-danger" data-bs-target="#ausbildung-{{ $training->getId() }}-loeschen" data-bs-toggle="modal">
                                            <x-icon name="trash" />
                                        </button>
                                    @endcan
                                </td>
                            </tr>

                            @can('trainings.delete')
                                <x-modal body_classes="text-center" form_action="{{ route('ausbildung.delete', [$training]) }}" id="ausbildung-{{ $training->getId() }}-loeschen">

                                    <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
                                    <x-slot:body>
                                        <div class="text-center">
                                            <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
                                        </div>
                                        <p class="text-warning">{!! __('general.ausbildung_loeschen_confirm', [
                                            'br' => '<br/>',
                                        ]) !!}</p>
                                    </x-slot:body>
                                    <x-slot:footer>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
                                        <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
                                    </x-slot:footer>
                                </x-modal>
                            @endcan

                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 mb-4 row">
            <h2>{{ __('general.abgeschlossene_ausbildungen') }}</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.qualifikation') }}</th>
                            <th>{{ __('general.ausbilder') }}</th>
                            <th>{{ __('general.treffpunkt') }}</th>
                            <th>{{ __('general.datum_uhrzeit') }}</th>
                            <th>{{ __('general.teilnehmeranzahl') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trainings_completed as $training)
                            <tr>
                                <td>{{ $training->getId() }}</td>
                                <td>{{ $training->getName() }}</td>
                                <td>{{ $training->getTrainerName() }}</td>
                                <td>{{ $training->getMeetingPoint() }}</td>
                                <td>{{ $training->getFormatedDate() }}</td>
                                <td>{{ $training->getCountParticipants() }} / {{ $training->getMaxParticipants() }}</td>
                                <td class="text-end">
                                    <a class="me-2 btn btn-sm btn-info" href="{{ route('ausbildung.show', $training) }}">
                                        <x-icon :hovertext="__('general.bearbeiten')" name="edit" />
                                    </a>
                                    @can('trainings.delete')
                                        <button class="cursor-pointer btn btn-sm btn-danger" data-bs-target="#ausbildung-{{ $training->getId() }}-loeschen" data-bs-toggle="modal">
                                            <x-icon :hovertext="__('general.loeschen')" name="trash" />
                                        </button>
                                    @endcan
                                </td>
                            </tr>

                            @can('trainings.delete')
                                <x-modal body_classes="text-center" form_action="{{ route('ausbildung.delete', [$training]) }}" id="ausbildung-{{ $training->getId() }}-loeschen">

                                    <x-slot:title>{{ __('general.loeschen') }}</x-slot:title>
                                    <x-slot:body>
                                        <div class="text-center">
                                            <x-icon :classes="['text-warning']" name="alert-triangle" width-height="48" />
                                        </div>
                                        <p class="text-warning">{!! __('general.ausbildung_loeschen_confirm', [
                                            'br' => '<br/>',
                                        ]) !!}</p>
                                    </x-slot:body>
                                    <x-slot:footer>
                                        <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
                                        <button class="btn btn-danger" type="submit">{{ __('general.loeschen') }}</button>
                                    </x-slot:footer>
                                </x-modal>
                            @endcan

                        @endforeach
                    </tbody>
                </table>
            </div>
            <x-paginate :data="$trainings_completed" />
        </div>
    </div>
@endsection
