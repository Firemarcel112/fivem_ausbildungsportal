<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Document;
use Illuminate\Support\Facades\DB;

class DeleteDocumentAction
{
    /**
     * @return ActionResult
     */
    public function execute(Document $model): ActionResult
    {
        return DB::transaction(function () use ($model) {

            $document_path = $model->url;

            $model->documentAssign()->delete();

            $model->delete();

            if (file_exists($document_path)) {
                unlink($document_path);
            }

            return new ActionResult(
                true,
                __('general.erfolgreich_geloescht')
            );
        });
    }
}
