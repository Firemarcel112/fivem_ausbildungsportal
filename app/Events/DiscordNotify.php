<?php

namespace App\Events;

use App\Enums\Training\Type;
use App\Models\Training;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DiscordNotify
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public array $notifications,
        public Type $type,
        public ?Training $model = null,
        public array $context = [],
    ) {}
}
