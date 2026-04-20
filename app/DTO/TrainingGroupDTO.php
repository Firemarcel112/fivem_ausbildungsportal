<?php

namespace App\DTO;

use Illuminate\Support\Collection;

class TrainingGroupDTO
{
    /**
     * Summary of __construct
     *
     * @param string                       $date_label
     * @param Collection<TrainingViewData> $items
     */
    public function __construct(
        public string $date_label,
        public Collection $items,
    ) {}
}
