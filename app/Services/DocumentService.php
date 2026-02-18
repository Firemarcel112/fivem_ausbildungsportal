<?php

namespace App\Services;

use Exception;
use RuntimeException;
use Str;
use App\DTO\ParticipantDTO;
use App\DTO\TrainerDTO;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\LaravelPdf\Facades\Pdf;

class DocumentService
{

    /**
     * Erstellt ein Zertifikat
     * @param ParticipantDTO $participant
     * @param TrainerDTO $trainer
     * @param string $qualification_name
     * @param string $training_date
     * @throws RuntimeException
     * @return string
     */
    public function createCertificate(
        ParticipantDTO $participant,
        TrainerDTO $trainer,
        string $qualification_name,
        string $training_date,
    ) {
        $certificate_overrides = [
            'participant_name' => Str::slug($participant->getFullName(), '_', 'de'),
            'qualification_name' => Str::slug($qualification_name, '_', 'de'),
        ];

        $certificate_name = 'Zertifikat_' . $certificate_overrides['participant_name'] . '_' . $certificate_overrides['qualification_name'] . '_' . now()->format('Y_m_d_His');

        $certificate_path = Storage::disk('certificates')
            ->path($certificate_name . '.pdf');

        try {
            $view_path = 'certificate.index';
            if (View::exists('certificate.custom')) {
                $view_path = 'certificate.custom';
            }

            $pdf = Pdf::view($view_path, [
                'salutation_trainer' => $trainer->salutation,
                'trainer_name' => $trainer->getFullName(),
                'training_date' => $training_date,

                'salutation' => $participant->salutation,
                'name' => $participant->getFullName(),
                'birth_date' => $participant->getBirthDate(),
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

    public function signPdf(string $pdf_path)
    {
        $pdf_service = new PdfService();
        $pdf_service->sign($pdf_path);
    }
}
