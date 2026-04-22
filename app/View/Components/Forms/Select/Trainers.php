<?php

namespace App\View\Components\Forms\Select;

use App\DTO\SimpleListItem;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Trainers extends Component
{
    /**
     * @var Collection<int, SimpleListItem>
     */
    public ?Collection $trainers = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name = 'trainer_id',
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select.trainers');
    }

    /**
     * @return Collection<int, SimpleListItem>
     */
    public function getTrainers(): Collection
    {
        return $this->trainers ??= User::getTrainers()
            ->map(fn(User $item) => new SimpleListItem(
                $item->getKey(),
                $item->account?->full_name
            ));
    }
}
