<?php

namespace App\View\Components\Navigation;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Item extends Component
{
    public string $url;

    public ?string $route_name = null;

    public ?string $icon;

    private array $route_names;

    /**
     * Create a new component instance.
     */
    public function __construct(string $url, array $parameters = [], ?string $icon = null)
    {
        $this->route_names = array_keys(Route::getRoutes()->getRoutesByName());
        $this->url = $url;
        $this->icon = $icon;

        if (in_array($url, $this->route_names)) {
            $this->url = route($this->url, $parameters);
            $this->route_name = $url;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation.item');
    }

    /**
     * Prüft ob dir Route Aktiv ist,
     * Funktioniert nur bei Routen von Laravel
     *
     * @return bool
     */
    public function isActive()
    {
        if (in_array($this->route_name, $this->route_names)) {
            return (request()?->route()?->getName() ?? null) == $this->route_name;
        }

        return false;
    }
}
