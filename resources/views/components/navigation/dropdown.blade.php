<li @class(['nav-item', 'dropdown', 'active' => $isActive()])>
    <a aria-expanded="false" class="text-white nav-link dropdown-toggle" data-bs-auto-close="outside" data-bs-toggle="dropdown" href="#navbar-base" role="button">
        <span class="nav-link-icon d-md-none d-lg-inline-block">
            @if (!empty($icon))
                <x-icon :classes="['icon', 'text-white']" name="{{ $icon }}" />
            @endif
        </span>
        {{ $text }}
    </a>
    <div class="dropdown-menu">
        @foreach ($items as $item)
            @continue(!$getItemPermission($item))
            <a @class(['dropdown-item', 'active' => $getDropdownActive($item)]) href="{{ $getDropdownUrl($item) }}">
                {{ $item['text'] }}
            </a>
        @endforeach
    </div>
</li>
