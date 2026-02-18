<?php

namespace App\Listeners\TrainingComplete;

use Exception;
use App\DTO\ParticipantDTO;
use App\DTO\TrainerDTO;
use App\Events\TrainingComplete;
use App\Models\Document;
use App\Models\User\Qualification as UserQualification;
use App\Services\DocumentService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AddQualification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TrainingComplete $event): void
    {
        $training = $event->training;
        $participant = $event->participant;
        $qualification = $event->training->qualification;

        $add_qualification = UserQualification::firstOrNew([
            'user_id' => $participant->getUserId(),
            'qualification_id' => $training->getQualificationId(),
        ]);
        $add_qualification->setTrainingId($training->getId());
        $add_qualification->save();

        if (!$qualification->getGenerateCertificate()) {
            return;
        }

        try {
            $document_url = app(DocumentService::class)
                ->createCertificate(
                    ParticipantDTO::fromModel($participant->account),
                    TrainerDTO::fromModel($training->trainer),
                    $qualification->getName(),
                    $training->getDate()->format('d.m.Y'),
                );

            Document::createDocument(
                'Zertifikat: ' . $qualification->getName(),
                $document_url,
                null,
                $participant->getUserId(),
                'ACCOUNT',
            );
        } catch (Exception $e) {
            Log::error($e);
            throw new Exception($e->getMessage());
        }
    }
}
