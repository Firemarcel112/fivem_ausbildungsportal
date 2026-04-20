<?php

namespace App\View\Components\Trainings\Modal;

use App\DTO\SimpleListItem;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Create extends Component
{
    /**
     * @var Collection<int, SimpleListItem>
     */
    public Collection $trainers;

    public string $default_meeting_point;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $default_meeting_point = null
    ) {
        $this->default_meeting_point = $default_meeting_point ?? config('settings.default_meeting_point');
    }

    /**
     * @return Collection<int, SimpleListItem>
     */
    protected function getTrainers(): Collection
    {
        return $this->trainers ??= User::with('account')
            ->withIsTrainer()
            ->get()
            ->map(fn(User $item) => new SimpleListItem(
                $item->getKey(),
                $item->account?->full_name
            ));
    }

    public function isSelectedTrainer(mixed $option_value): bool
    {
        $current_value = old('trainer_id', request('trainer_id') ?? '');

        return $current_value == $option_value;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $this->trainers = $this->getTrainers();

        return view('components.trainings.modal.create');
    }
}
