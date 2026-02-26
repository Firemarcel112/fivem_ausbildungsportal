<?php

namespace App\View\Components;

use App\Models\Fractions\Fraction;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class DiscordNotification extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct() {}

    /**
     * Get the fractions for the component.
     *
     * @return Collection<int, Fraction>|Collection<int, Fraction>
     */
    public function getFractions(): Collection
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
