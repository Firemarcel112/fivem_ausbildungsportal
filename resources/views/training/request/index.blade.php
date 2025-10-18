@extends('layouts.app')

@section('content')
    <div class="container-xl">
        <div class="mt-2 mb-4 row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    {{ __('general.ausbildungswuensche') }}
                </div>
                <h2 class="page-title">
                    {{ __('general.uebersicht') }}
                </h2>
            </div>
        </div>
        @include('default.alerts')

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>{{ __('Datum') }}</th>
                    <th>{{ __('Name') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $date => $value)
                    @foreach ($value as $name => $users)
                        <tr>
                            <td>{{ $date }}</td>
                            <td>
                                <span class="d-block">{{ $name }}</span>

                                <div class="accordion-flush" id="collapse-id-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    <div class="accordion-item">
                                        <div class="accordion-header">
                                            <button aria-controls="collapse-{{ $loop->parent->index }}-{{ $loop->index }}" aria-expanded="false" class="accordion-button collapsed" data-bs-target="#collapse-{{ $loop->parent->index }}-{{ $loop->index }}" data-bs-toggle="collapse">
                                                {{ __('general.angefordert_von') }}
                                                <div class="accordion-button-toggle">
                                                    <x-icon name="chevron-down" />
                                                </div>
                                            </button>

                                        </div>
                                        <div class="accordion-collapse collapse" data-bs-parent="#collapse-{{ $loop->parent->index }}-{{ $loop->index }}" id="collapse-{{ $loop->parent->index }}-{{ $loop->index }}">
                                            <div class="accordion-body">
                                                <ul style="list-style: none; padding-left: 0;">
                                                    @foreach ($users['users'] as $user)
                                                        <li>{{ $user['name'] }} - ({{ $user['id'] }})</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- <button aria-controls="collapse-{{ $loop->parent->index }}-{{ $loop->index }}" aria-expanded="false" class="btn btn-link p-0" data-bs-target="#collapse-{{ $loop->parent->index }}-{{ $loop->index }}" data-bs-toggle="collapse" type="button">
                                    <span class="text-danger">{{ __('general.angefordert_von') }}</span>
                                </button>
                                <div class="collapse mt-2" id="collapse-{{ $loop->parent->index }}-{{ $loop->index }}">
                                    <ul class="list-unstyled mb-0">
                                        @foreach ($users['users'] as $user)
                                            <li>{{ $user['name'] }} - ({{ $user['id'] }})</li>
                                        @endforeach
                                    </ul>
                                </div> --}}
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
