@php
    /** @var \App\DTO\SimpleUserViewData|null $auth_user */
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('layouts.head')

    <body data-bs-theme="{{ config('app.theme') ?? 'dark' }}">
        <div class="page">
            @include('layouts.header')
            <div class="page-wrapper" role="main">
                <div class="page-body">
                    @yield('content')
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </body>

</html>
