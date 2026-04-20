<?php

namespace App\View\Components;

use App\DTO\TrainingViewData;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TrainingCard extends Component
{
    public TrainingViewData $training;

    public int $enroll_deadline_in_minutes;

    /**
     * Create a new component instance.
     */
    public function __construct(TrainingViewData $training, ?int $enrollDeadlineInMinutes = null)
    {
        $this->training = $training;
        $this->enroll_deadline_in_minutes = $enrollDeadlineInMinutes ?? config('settings.enroll_deadline');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.training-card');
    }
}
