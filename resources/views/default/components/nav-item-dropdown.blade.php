@props([
    'links_in_column' => 10,
])

<li @class([
    'nav-item',
    'dropdown',
    'active' => in_array(
        request()->route()->getName(),
        collect($items)->pluck('route_name')->toArray()),
])>
    <a aria-expanded="false" class="nav-link dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="#navbar-base" role="button">

        <span class="nav-link-icon d-md-none d-lg-inline-block">
            @if (!empty($icon))
                <x-icon :classes="['icon', 'text-white']" name="{{ $icon }}" />
            @endif
        </span>

        <span class="nav-link-title text-white">
            {{ $text }}
            @if (!empty($is_new))
                <span class='badge badge-sm bg-green text-white text-uppercase ms-auto'>
                    {{ __('general.new') }}
                </span>
            @endif
        </span>
    </a>
    <div class="dropdown-menu">
        <div class="dropdown-menu-columns">
            @foreach ($items as $item)
                @php
                    if (!empty($item['route_name'])) {
                        $route_name = $item['route_name'];
                    } else {
                        $route_name = null;
                    }
                @endphp
                @if ($loop->first || $loop->index % $links_in_column == 0)
                    <div class="dropdown-menu-column">
                @endif
                <a @class([
                    'dropdown-item',
                    'active' => request()->route()->getName() == $route_name,
                ]) href="{{ $item['url'] ?? !empty($item['route_name']) ? route($item['route_name']) : '' }}">
                    {{ $item['text'] }}

                    @if (!empty($item['is_new']))
                        <span class='badge badge-sm bg-green-lt text-uppercase ms-auto'>
                            {{ __('general.new') }}
                        </span>
                    @endif
                </a>
                @if ($loop->index % $links_in_column == $links_in_column - 1 || $loop->last)
        </div>
        @endif
        @endforeach
    </div>
    </div>
</li>
