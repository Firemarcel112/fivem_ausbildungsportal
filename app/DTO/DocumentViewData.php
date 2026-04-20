<?php

namespace App\DTO;

use App\Models\Document;
use App\Models\User;

class DocumentViewData
{
    public function __construct(
        public int $id,
        public string $title,
        public string $type,
        public string $created,
        public string $assign_to_name,
        public ?string $assign_to_url = null,
        public ?int $assign_to_id = null,
        public bool $permission_update = false,
        public bool $permission_delete = false,
    ) {}

    public static function fromModel(
        Document $model,
        User $user,
    ) {

        return new self(
            $model->getKey(),
            $model->title,
            $model->type,
            $model->created_at->format('d.m.Y'),
            $model->assign_info['name'],
            $model->assign_info['url'],
            $model->assign_info['id'],
            $user->can('update', $model),
            $user->can('delete', $model),
        );
    }
}
