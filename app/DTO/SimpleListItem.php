<?php

namespace App\DTO;

readonly class SimpleListItem
{
    public int|string $id;

    public string $label;

    public ?string $description;

    public array $extra;

    public function __construct(
        int|string $id,
        string $label,
        ?string $description = null,
        array $extra = [],
    ) {
        $this->id = $id;
        $this->label = $label;
        $this->description = $description;
        $this->extra = $extra;
    }
}
