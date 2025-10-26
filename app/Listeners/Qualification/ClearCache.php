<?php

namespace App\Listeners\Qualification;

use App\Events\QualificationAction;

class ClearCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(QualificationAction $event): void
    {
        cache()->forget('qualifications_all');
    }
}
