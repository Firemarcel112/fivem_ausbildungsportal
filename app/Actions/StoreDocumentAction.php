<?php

namespace App\Actions;

use App\DTO\ActionResult;
use App\Models\Document;
use App\Models\DocumentLink;
use Arr;
use Illuminate\Support\Facades\DB;

class StoreDocumentAction
{
    /**
     * @param  array<string, mixed> $data
     * @return ActionResult
     */
    public function execute(array $data): ActionResult
    {
        return DB::transaction(function () use ($data) {

            $document_data = Arr::except($data, [
                'link_id',
                'link_type',
            ]);

            $document = Document::create($document_data);

            $document_link_data = Arr::only($data, [
                'link_id',
                'link_type',
            ]);

            if (!is_null($document_link_data['link_id'] ?? null) && !is_null($document_link_data['link_type'] ?? null)) {
                $document_link_data['document_id'] = $document->getQueueableId();
                DocumentLink::create($document_link_data);
            }

            return new ActionResult(
                true,
                __('general.erfolgreich_angelegt')
            );
        });
    }
}
