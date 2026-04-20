<?php

namespace App\DTO;

use App\Models\Fraction;

class FractionViewData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $short_name,
        public string $full_name,
        public ?string $webhook_url,
    ) {}

    public static function fromModel(Fraction $model): self
    {

        return new self(
            $model->getKey(),
            $model->name,
            $model->short_name,
            $model->full_name,
            $model->discord_webhook,
        );
    }
}
