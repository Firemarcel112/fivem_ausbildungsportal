<header class="navbar navbar-expand-md d-print-none" data-bs-theme="dark">
    <div class="container-xl">
        <button aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler collapsed" data-bs-target="#navbar-menu" data-bs-toggle="collapse" type="button">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
            <a aria-label="{{ config('app.name') }}" href="{{ route('home') }}">
                @if (File::exists('storage/images/logo.png'))
                    <img class="navbar-brand-image" src="{{ asset('storage/images/logo.png') }}">
                @endif
            </a>
        </div>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex">
                <div class="nav-item">
                    <a class="px-0 text-white app-js-switch-theme nav-link hide-theme-light">
                        <x-icon :classes="['icon', 'cursor-pointer']" :hovertext="__('general.tag_modus_aktivieren')" name="moon" />
                    </a>

                    <a class="px-0 text-white app-js-switch-theme nav-link hide-theme-dark">
                        <x-icon :classes="['icon', 'cursor-pointer']" :hovertext="__('general.nacht_modus_aktivieren')" name="sun" />
                    </a>
                </div>
            </div>
            @guest
                <div class="nav-item">
                    <a class="text-white cursor-pointer" href="{{ route('login.index') }}">
                        <x-icon :hovertext="__('general.anmelden')" name="login" />
                    </a>
                </div>
            @endguest
            @auth
                <div class="nav-item dropdown">
                    <a aria-label="Open user menu" class="p-0 nav-link d-flex lh-1 text-reset" data-bs-toggle="dropdown" href="#">
                        <x-avatar :user="auth()->user()" size='sm'></x-avatar>

                        <div class="text-white d-none d-xl-block ps-2">
                            <div class="text-nowrap">{{ auth()->user()->getSalutation() }} {{ auth()->user()->getDiscordName() }}</div>
                            <div class="mt-1 text-white small">
                                {{ auth()->user()->account->getDefaultFraction()->getName() }} ({{ auth()->user()->account->getDefaultFraction()->getShortName() }})
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <a class="dropdown-item" href="{{ route('profile.show', auth()->user()) }}">{{ __('general.profil') }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('user.show', auth()->user()) }}">{{ __('general.einstellungen') }}</a>
                        <a class="dropdown-item" href="{{ route('logout') }}">{{ __('general.ausloggen') }}</a>
                    </div>
                </div>
            @endauth
        </div>
        <div class="navbar-collapse collapse" id="navbar-menu" style="">
            <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
                <ul class="navbar-nav">
                    <x-navigation.item icon="home" url="home">
                        <x-slot:text>{{ __('general.startseite') }}</x-slot:text>
                    </x-navigation.item>

                    <x-navigation.item icon="shield-question" url="requirements.index">
                        <x-slot:text>{{ __('general.voraussetzungen') }}</x-slot:text>
                    </x-navigation.item>

                    @canAny(['trainings.show', 'trainings.requests'])
                        <x-navigation.dropdown :items="[
                            [
                                'text' => __('general.ausbilder_dashboard'),
                                'route_name' => 'ausbilder.index',
                                'permission' => 'trainings.show',
                            ],
                            [
                                'text' => __('general.ausbildungswuensche'),
                                'route_name' => 'trainings.request.index',
                                'permission' => 'trainings.requests',
                            ],
                        ]" :text="__('general.ausbilder')" icon="login" />
                    @endcan

                    @can('usermanagement.index')
                        <x-navigation.item icon="users" url="usermanagement.index">
                            <x-slot:text>{{ __('general.benutzerverwaltung') }}</x-slot:text>
                        </x-navigation.item>
                    @endcan

                    @can('documents.show.account')
                        <x-navigation.item icon="file" url="documents.index">
                            <x-slot:text>{{ __('general.dokumente') }}</x-slot:text>
                        </x-navigation.item>
                    @endcan

                    <x-navigation.dropdown :items="[
                        [
                            'text' => __('general.fraktionen'),
                            'route_name' => 'administration.fractions.index',
                            'permission' => 'administration.fractions.edit',
                        ],
                        [
                            'text' => __('general.qualifikationen'),
                            'route_name' => 'administration.qualifications.index',
                            'permission' => 'administration.qualifications.edit',
                        ],
                        [
                            'text' => __('general.rollen'),
                            'route_name' => 'admin.roles.index',
                            'permission' => 'administration.roles.edit',
                        ],
                        [
                            'text' => __('general.einstellungen'),
                            'route_name' => 'admin.settings.index',
                            'permission' => 'administration.settings.edit',
                        ],
                    ]" :text="__('general.administration')" icon="login" />
                </ul>
            </div>
        </div>
    </div>
</header>
