<?php

namespace App\Jobs;

use App\Actions\StoreDocumentAction;
use App\DTO\ParticipantDTO;
use App\DTO\SimpleUserViewData;
use App\DTO\TrainingParticipantViewData;
use App\Models\Qualification;
use App\Services\DocumentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class CreateCertificate implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public TrainingParticipantViewData|ParticipantDTO $participant,
        public SimpleUserViewData $trainer,
        public Qualification $qualification,
        public string $training_date,
        public ?int $account_id = null,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(
        DocumentService $document_service,
        StoreDocumentAction $store_document_action
    ): void {

        $document_url = $document_service
            ->createCertificate(
                $this->participant,
                $this->trainer,
                $this->qualification->name,
                $this->training_date,
            );

        $store_document_action->execute([
            'title' => str('Zertifikat: ')->append($this->qualification->name)->value(),
            'url' => $document_url,
            'link_id' => $this->account_id,
            'link_type' => 'ACCOUNT',
        ]);
    }
}
