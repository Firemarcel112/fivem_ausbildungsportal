<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class UserTrainingBanAlert extends Component
{
    public ?User $user;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        return $this->hasTrainingBan() ? view('components.user-training-ban-alert') : '';
    }

    /**
     * Gibt zurück ob der User eine Aktive Ausbildungssperre hat
     *
     * @return bool
     */
    public function hasTrainingBan(): bool
    {

        return $this->user?->activeTrainingBan?->count() > 0;
    }

    /**
     * @return array{date_from: string|\Illuminate\Support\Carbon, date_to: string|\Illuminate\Support\Carbon, issuer: mixed, reason: string}
     */
    public function getTrainingBanData(): array
    {
        /**
         * @var \App\Models\User $user;
         */
        $user = $this->user;

        /**
         * @var \App\Models\Trainings\TrainingBan $training_ban
         */
        $training_ban = $user->activeTrainingBan;

        $training_ban->load('issuer');

        return [
            'issuer' => $training_ban->issuer_name,
            'date_from' => $training_ban->date_from->format('d.m.Y'),
            'date_to' => $training_ban->date_to->format('d.m.Y'),
            'reason' => nl2br($training_ban->reason),
        ];
    }
}
