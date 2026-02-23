<?php

namespace App\View\Components\Trainings\Modal;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Qualifications\Qualification;
use App\Models\User;
use Cache;
use Illuminate\Database\Eloquent\Collection;

class Create extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    public function getTrainers(): Collection
    {
        return User::with(['permissions', 'account'])
            ->withIsTrainer()
            ->get();
    }

    public function getQualifications(): Collection
    {
        return Cache::rememberForever('qualifications.all', function () {
            return Qualification::isVisible()
                ->isOrderByDefault()
                ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.trainings.modal.create');
    }
}
