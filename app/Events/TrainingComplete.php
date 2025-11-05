<?php

namespace App\Events;

use App\Models\Trainings\Participant;
use App\Models\Trainings\Training;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingComplete
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Training $training,
        public Participant $participant,
    ) {}
}
