<?php

namespace App\Events;

use App\Models\Qualification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QualificationAction
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Qualification $qualification;

    /**
     * Create a new event instance.
     */
    public function __construct(Qualification $qualification)
    {
        $this->qualification = $qualification;
    }
}
