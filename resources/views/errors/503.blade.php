@extends('layouts.errors')


@section('content')
    <div class="container-tight py-4">
        <div class="empty">
            <div class="empty-img">
                <x-icon name="tool" width-height="96" />
            </div>
            <p class="empty-title text-warning">Wartungsmodus</p>
            <p class="empty-subtitle text-secondary">
                Die Seite befindet sich aktuell im Wartungsmodus!
            </p>
        </div>
    </div>
@endsection
