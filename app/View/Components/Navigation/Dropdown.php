<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $icon,
        public string $text,
        public array $items,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $can_see = true;
        $any_permission = collect($this->items)->pluck('permission')->toArray();
        $has_no_permission = in_array('', $any_permission, true) || in_array(null, $any_permission, true);
        $any_permission = array_filter($any_permission);
        if (!empty($any_permission) && !$has_no_permission) {
            if (!auth()?->user()?->hasAnyPermission($any_permission)) {
                $can_see = false;
            }
        }
        return $can_see ? view('components.navigation.dropdown') : '';
    }

    public function isActive()
    {
        return in_array(request()->route()->getName(), collect($this->items)->pluck('route_name')->toArray());
    }

    public function getDropdownUrl($item)
    {
        if (isset($item['route_name'])) {
            return route($item['route_name']);
        } elseif (isset($item['url'])) {
            return $item['url'];
        } else {
            return '#';
        }
    }

    public function getDropdownActive($item)
    {
        return request()->route()->getName() === ($item['route_name'] ?? false) ? true : false;
    }

    public function getItemPermission($item)
    {
        if (isset($item['permission'])) {
            return auth()?->user()?->can($item['permission']) ?? false;
        }
        return true;
    }
}
