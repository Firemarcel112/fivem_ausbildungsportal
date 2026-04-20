<?php

namespace App\View\Components\Forms\Select;

use App\DTO\SimpleListItem;
use App\Models\Qualification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Qualifications extends Component
{
    /**
     * Summary of qualifications
     *
     * @var Collection<SimpleListItem>
     */
    private Collection $qualifications;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'qualification_id',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.forms.select.qualifications');
    }

    /**
     * @return Collection<int, SimpleListItem>
     */
    public function getQualifications()
    {
        return $this->qualifications ??= Qualification::getAllQualifications()
            ->map(fn(Qualification $item) => new SimpleListItem(
                $item->getKey(),
                $item->name
            ));
    }

    public function isSelected(mixed $option_value): bool
    {
        $current_value = old($this->name, request($this->name) ?? '');

        return $current_value == $option_value;
    }
}
