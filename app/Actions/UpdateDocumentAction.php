<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Document;
use Arr;
use Illuminate\Support\Facades\DB;

class UpdateDocumentAction
{
    /**
     * @param  array<string, mixed> $data
     * @return ActionResult
     */
    public function execute(Document $model, array $data): ActionResult
    {
        return DB::transaction(function () use ($model, $data) {

            $model->update(Arr::except($data, 'assign_info'));
            $model->assign_info = data_get($data, 'assign_info');

            return new ActionResult(
                true,
                __('general.erfolgreich_aktualisiert')
            );
        });
    }
}
