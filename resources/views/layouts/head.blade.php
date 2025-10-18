<head>
    <title>{{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    @livewireStyles
    <link href="{{ asset('css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    @livewireScripts
    <script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-select.min.js') }}"></script>
    <script>
        var app_global = {
            theme: "{{ config('app.theme') }}"
        }
    </script>
    <script src="{{ asset('js/common.js') }}"></script>
</head>
