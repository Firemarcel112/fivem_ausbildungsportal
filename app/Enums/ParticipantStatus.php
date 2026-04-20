<?php

namespace App\Enums;

enum ParticipantStatus: int
{
    /** Abwesend */
    case ABSENCE = 0;

    /** Bestanden */
    case PASSED = 1;

    /** Nur Anwesend */
    case ONLY_PRESENT = 2;

    /** Abgemeldet */
    case LOGGED_OUT = 3;

    public function getLabel(): string
    {
        return match ($this->name) {
            'PASSED' => __('general.bestanden'),
            'ONLY_PRESENT' => __('general.nur_anwesend'),
            'LOGGED_OUT' => __('general.abgemeldet'),
            'ABSENCE' => __('general.abwesend'),
        };
    }

    public function getBadgeColorClass(): string
    {
        return match ($this->name) {
            'PASSED' => 'bg-success',
            'ONLY_PRESENT' => 'bg-warning',
            'LOGGED_OUT' => 'bg-red',
            'ABSENCE' => 'bg-red',
        };
    }
}
