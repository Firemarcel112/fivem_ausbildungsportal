@extends('layouts.app')

@section('content')
    <div class="container-xl space-y">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.dokumente') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    @can('documents.create')
                        <a class="btn btn-primary" data-bs-target="#create-new-document" data-bs-toggle="modal">
                            <x-icon :classes="['me-2']" name="plus" />
                            {{ __('general.zertifikat_erstellen') }}
                        </a>
                        @include('documents.modals.create')
                    @endcan
                </div>
            </div>
            @include('default.alerts')
        </div>
        <div class="row">
            <form action="?" class="space-y" method="POST">
                @csrf
                <div class="row g-2">
                    <div class="col">
                        <x-forms.input name="search">{{ __('general.suche') }}</x-forms.input>
                    </div>
                    <div class="col-md-3 col-12">
                        <x-forms.input :default="$items_per_page" name="items_per_page" type="number">
                            {{ __('general.anzahl_pro_seite') }}
                        </x-forms.input>
                    </div>
                    <div class="col-md-3 col-12">
                        <x-forms.select label="{{ __('general.sortieren_nach') }}" name="sort_by">
                            <option @selected(old('sort_by', 'created_at') == 'created_at') value="created_at">{{ __('general.erstellt_am') }}</option>
                            <option @selected(old('sort_by', 'created_at') == 'assigned') value="assigned">{{ __('general.nicht_zugeordnet') }}</option>
                        </x-forms.select>
                    </div>
                    <div class="col-12 text-end">
                        <x-forms.button>{{ __('general.suchen') }}</x-forms.button>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-2 row">
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>{{ __('general.id') }}</th>
                            <th>{{ __('general.name') }}</th>
                            <th>{{ __('general.typ') }}</th>
                            <th>{{ __('general.zugeordnet_zu') }}</th>
                            <th>{{ __('general.erstellt_am') }}</th>
                            @canAny(['documents.edit', 'documents.delete'])
                                <th></th>
                            @endcanAny
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data ?? [] as $document)
                            <tr>
                                <td>{{ $document->getId() }}</td>
                                <td><a href="{{ route('documents.show', $document) }}" target="_blank">{{ $document->getTitle() }} <x-icon name="external-link"></x-icon></a></td>
                                <td>{{ $document->type }}</td>
                                <td>
                                    @if (!empty($document->assign['url']))
                                        <a href="{{ $document->assign['url'] }}" target="_blank">{{ $document->assign['name'] }}
                                            <x-icon name="external-link" />
                                        </a>
                                    @else
                                        {{ $document->assign['name'] }}
                                    @endif
                                </td>
                                <td>
                                    {{ $document->getCreated()->format('d.m.Y') }}
                                </td>
                                @canAny(['documents.edit', 'documents.delete'])
                                    <td class="text-end">
                                        @can('documents.edit')
                                            <a class="btn btn-sm btn-primary" href="{{ route('documents.edit', $document) }}">
                                                <x-icon :classes="['text-white']" :hovertext="__('general.bearbeiten')" name="edit" />
                                            </a>
                                        @endcan
                                        @can('documents.delete')
                                            @include('documents.modals.delete', [
                                                'document' => $document,
                                            ])
                                            <a class="btn btn-sm btn-danger" data-bs-target="#document-{{ $document->getId() }}-delete" data-bs-toggle="modal">
                                                <x-icon :classes="['text-white']" :hovertext="__('general.loeschen')" name="trash" />
                                            </a>
                                        @endcan
                                    </td>
                                @endcanAny
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">
                                    <span style="fw-bold text-muted">{{ __('general.keine_dokumente_verfuegbar') }}</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                <div class="mt-4">
                    <x-paginate :$data />
                </div>
            </div>
        </div>
    </div>
@endsection
