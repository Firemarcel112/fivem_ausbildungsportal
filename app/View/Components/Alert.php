<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public string $type;
    /**
     * Create a new component instance.
     */
    public function __construct(string $type = 'info')
    {
        $this->type = $type;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.alert');
    }

    /**
     * Gibt die  Klasse für dass Alert zurück
     * @return string
     */
    public function getClass()
    {
        switch (strtolower($this->type)) {
            case 'info':
                $alert_class = 'alert-info';
                break;
            case 'success':
                $alert_class = 'alert-success';
                break;
            case 'error':
            case 'danger':
                $alert_class = 'alert-danger';
                break;
            case 'warning':
                $alert_class = 'alert-warning';
                break;
            default:
                $alert_class = 'alert-info';
                break;
        }
        return $alert_class;
    }

    /**
     * Gibt dass richtige Icon zurück
     * @return string
     */
    public function getIcon()
    {
        switch (strtolower($this->type)) {
            case 'info':
                $icon = 'info-circle';
                break;
            case 'success':
                $icon = 'check';
                break;
            case 'error':
                $icon = 'info-circle';
                break;
            case 'warning':
                $icon = 'alert-triangle';
                break;
            default:
                $icon = 'info-circle';
                break;
        }
        return $icon;
    }
}
