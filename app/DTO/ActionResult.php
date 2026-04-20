<?php

namespace App\DTO;

use Illuminate\Database\Eloquent\Model;

readonly class ActionResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?Model $model = null,
    ) {}
}
