@use('App\Facades\Alert')
@foreach (session('alerts') ?? [] as $alert)
    <x-alert :static="$alert['static'] ?? false" :type="$alert['type']">{!! $alert['message'] !!}</x-alert>
@endforeach
@foreach ($errors->all() ?? [] as $error)
    <x-alert type="error">{!! $error !!}</x-alert>
@endforeach
@php
    Alert::clearAlerts();
@endphp
