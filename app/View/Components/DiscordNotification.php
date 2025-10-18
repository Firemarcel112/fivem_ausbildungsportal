<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Fractions\Fraction;

class DiscordNotification extends Component
{

    /**
     * Create a new component instance.
     */
    public function __construct() {}

    public function getFractions()
    {
        return Fraction::get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.discord_notification');
    }
}
