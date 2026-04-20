<?php

namespace App\DTO;

use App\Models\Trainings\Request;

class TrainingRequestViewData
{
    public function __construct(
        public string $date,
        public string $time,
        public SimpleListItem $qualification,
        public SimpleListItem $requested_user,
    ) {}

    public static function fromModel(Request $model): self
    {

        return new self(
            $model->date->format('d.m.Y'),
            $model->time->format('H:i'),
            new SimpleListItem(
                $model->qualification_id,
                $model->qualification->name,
            ),
            new SimpleListItem(
                $model->user_id,
                $model->user->account->full_name,
                "({$model->user->account->getDefaultFraction()->short_name})",
            ),
        );
    }
}
