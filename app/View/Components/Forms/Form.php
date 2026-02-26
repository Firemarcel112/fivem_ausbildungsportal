<?php

namespace App\View\Components\Forms;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public string $method;

    public string $action;

    public ?string $route_action_name = null;

    /**
     * Create a new component instance.
     */
    public function __construct(string $method = 'GET', string $action = '/', ?string $actionName = null)
    {
        $this->method = $method;
        $this->action = $action;
        if (!empty($actionName)) {
            $this->route_action_name = route($actionName);
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.form');
    }
}
