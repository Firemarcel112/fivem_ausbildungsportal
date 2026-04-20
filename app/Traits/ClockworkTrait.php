<?php

namespace App\Traits;

trait ClockworkTrait
{
    /**
     * Startet ein Clockwork Event
     *
     * @param  string  $event
     * @param  ?string $name
     * @param  string  $color
     * @return void
     */
    public function beginClockwork(string $event, ?string $name = null, string $color = 'red')
    {
        if (empty($name)) {
            $name = $event;
        }
        if (config('clockwork.enable')) {
            clock()->event($event)->name($name)->color($color)->begin();
        }
    }

    /**
     * Beendet ein Clockwork Event
     *
     * @param  string $event
     * @return void
     */
    public function endClockwork(string $event)
    {
        if (config('clockwork.enable')) {
            clock()->event($event)->end();
        }
    }
}
