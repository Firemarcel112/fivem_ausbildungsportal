<?php

namespace App\View\Components;

use App\DTO\FractionViewData;
use App\Models\Fraction;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class DiscordNotification extends Component
{
    private Collection $fractions;

    /**
     * Create a new component instance.
     */
    public function __construct() {}

    /**
     * Get the fractions for the component.
     *
     * @return Collection<int, FractionViewData>
     */
    public function getFractions(): Collection
    {
        return $this->fractions ??= Fraction::get()
            ->map(fn(Fraction $item) => FractionViewData::fromModel($item));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.discord_notification');
    }
}
