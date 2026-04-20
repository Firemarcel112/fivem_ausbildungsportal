<?php

namespace App\View\Components\Forms\Select;

use App\Enums\Gender;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Genders extends Component
{
    private Collection $genders;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'gender',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select.genders');
    }

    /**
     * @return Collection<int, SimpleListItem>
     */
    public function getGenders(): Collection
    {
        return $this->genders ??= Gender::forSelect();
    }

    public function isSelected(mixed $option_value): bool
    {
        $current_value = old($this->name, request()->get($this->name) ?? '');

        return $current_value == $option_value;
    }
}
