<?php

namespace App\Services;

use App\DTO\SimpleUserViewData;
use App\DTO\TrainingParticipantViewData;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use RuntimeException;
use Spatie\LaravelPdf\Facades\Pdf;
use Str;

class DocumentService
{
    /**
     * Erstellt ein Zertifikat
     *
     * @param  TrainingParticipantViewData $participant
     * @param  SimpleUserViewData          $trainer
     * @param  string                      $qualification_name
     * @param  string                      $training_date
     * @return string
     *
     * @throws RuntimeException
     */
    public function createCertificate(
        TrainingParticipantViewData $participant,
        SimpleUserViewData $trainer,
        string $qualification_name,
        string $training_date,
    ) {
        $certificate_overrides = [
            'participant_name' => Str::slug($participant->full_name, '_', 'de'),
            'qualification_name' => Str::slug($qualification_name, '_', 'de'),
        ];

        $certificate_name = str('Zertifikat_')
            ->append($certificate_overrides['participant_name'])
            ->append('_')
            ->append($certificate_overrides['qualification_name'])
            ->append('_')
            ->append(now()->format('Y_m_d_His'));

        $certificate_path = Storage::disk('certificates')
            ->path($certificate_name->append('.pdf'));

        try {
            $view_path = 'certificate.index';
            if (View::exists('certificate.custom.' . $certificate_overrides['qualification_name'])) {
                $view_path = 'certificate.custom.' . $certificate_overrides['qualification_name'];
            } elseif (View::exists('certificate.custom.index')) {
                $view_path = 'certificate.custom.index';
            }

            $pdf = Pdf::view($view_path, [
                'salutation_trainer' => $trainer->salutation,
                'trainer_name' => $trainer->full_name,
                'training_date' => $training_date,

                'salutation' => $participant->salutation,
                'name' => $participant->full_name,
                'birth_date' => $participant->date_of_birth->format('d.m.Y'),
                'birth_location' => $participant->birth_location,
                'qualification' => $qualification_name,
            ])
                ->format('A4')
                ->paperSize(210, 297);

            $pdf = $pdf->save($certificate_path);

            $this->signPdf($certificate_path);

            return Storage::disk('certificates')
                ->path($certificate_name . '.pdf');
        } catch (Exception $e) {
            if (file_exists($certificate_path)) {
                unlink($certificate_path);
            }
            throw new RuntimeException('Fehler beim Erstellen des Zertifikats: ' . $e->getMessage());
        }
    }

    public function signPdf(string $pdf_path): void
    {
        $pdf_service = new PdfService;
        $pdf_service->sign($pdf_path);
    }
}
