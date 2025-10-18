<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.head')

    <body data-bs-theme="{{ config('app.theme') ?? 'dark' }}">
        <div class="page page-center">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="container-xl">
                        @yield('content')
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </body>

</html>
