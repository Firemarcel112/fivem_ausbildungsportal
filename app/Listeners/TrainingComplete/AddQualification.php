<?php

namespace App\Listeners\TrainingComplete;

use Exception;
use App\Events\TrainingComplete;
use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\User\Qualification as UserQualification;
use App\Services\PdfService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

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

        $participant_name = str_replace([' ', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü',], ['_', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue'], $participant->getFullName());
        $certificate_name = 'Zertifikat_' . $participant_name;
        $qualification_name = str_replace([' ', 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü'], ['_', 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue'], $qualification->getName());
        $certificate_name = $certificate_name . '_' . $qualification_name;
        $certificate_name = $certificate_name . '_' . now()->format('Y_m_d_His');

        $certificate_path = Storage::disk('certificates')->path($certificate_name . '.pdf');

        try {
            Pdf::view('certificate.index', [
                'trainer_name' => $training->getTrainerName(),
                'training_date' => $training->getDate()->format('d.m.Y'),
                'name' => $participant->getFullName(),
                'birth_date' => $participant->getBirthDate(),
                'birth_location' => $participant->account->getBirthLocation(),
                'qualification' => $qualification->getName(),
                'salutation' => $participant->account->getSalutation(),
                'salutation_trainer' => $training->trainer->getSalutation(),
            ])
                ->format('A4')
                ->paperSize(210, 297)
                ->save($certificate_path);

            $pdf_service = new PdfService();
            $pdf_service->sign($certificate_path);

            $url = Storage::disk('certificates')->path($certificate_name . '.pdf');

            $document_model = new Document();
            $document_model->setTitle('Zertifikat: ' . $qualification->getName());
            $document_model->setUrl($url);
            $document_model->save();

            $document_link = new DocumentLink();
            $document_link->setDocumentId($document_model->getId());
            $document_link->setLinkId($participant->getUserId());
            $document_link->setLinkType('ACCOUNT');
            $document_link->save();
        } catch (Exception $e) {
            if (!empty($document_model)) {
                if ($document_model->exists) {
                    $document_model->delete();
                }
            }
            if (!empty($certificate_path)) {
                if (file_exists($certificate_path)) {
                    unlink($certificate_path);
                }
            }
            Log::error($e);
            throw new Exception($e->getMessage());
        }
    }
}
