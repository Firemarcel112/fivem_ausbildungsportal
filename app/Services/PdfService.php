<?php

namespace App\Services;

use Exception;
use Storage;
use Illuminate\Support\Facades\Process;

class PdfService
{
    /**
     * Pfad zur P12 Datei
     * @var string
     */
    protected string $p12_path;

    /**
     * P12 Passwort
     *
     * @var string
     */
    protected string $p12_password;

    /**
     * Pfad zur JSignPdf Datei
     * @var string
     */
    protected string $jar_path;

    public function __construct()
    {
        $this->p12_path = Storage::disk('local')->path(config('app.pdf_sign.p12_key_name') . '.p12');
        $this->p12_password = config('app.pdf_sign.p12_password');
        $this->jar_path = config('app.pdf_sign.jsign_path');
    }

    /**
     * Signiert eine PDF-Datei mit jSignPdf.
     *
     * @param string $pdf_path Pfad zur bestehenden PDF-Datei
     * @return bool|string
     */
    public function sign(string $pdf_path)
    {
        if (empty($this->jar_path) || empty($this->p12_password) || empty($this->jar_path)) {
            throw new Exception('JSignPDF nicht richtig konfiguriert');
        }
        if (!file_exists($pdf_path)) {
            throw new Exception("PDF nicht gefunden: {$pdf_path}");
        }
        $certificate_path = Storage::disk('certificates')->path('');

        $temp_file = $pdf_path;
        $temp_file_name = $pdf_path;
        $temp_file_name = str_replace(
            [$certificate_path, '.pdf'],
            '',
            $temp_file_name,
        );
        $temp_file = $certificate_path . $temp_file_name . '_tmp.pdf';

        copy($pdf_path, $temp_file);

        $cmd = [
            'java',
            '-jar',
            $this->jar_path,
            '-cl CERTIFIED_NO_CHANGES_ALLOWED',
            '--keystore-file',
            $this->p12_path,
            '--keystore-password',
            $this->p12_password,
            '--keystore-type',
            'PKCS12',
            '-d',
            $certificate_path,
            '-os',
            '_signed',
            $temp_file,
        ];

        $cmd[] = $pdf_path;

        $result = Process::run($cmd);

        if ($result->successful()) {

            $tmp_signed_file = str_replace(['.pdf'], '', $temp_file);
            $tmp_signed_file = $tmp_signed_file . '_signed.pdf';
            rename($tmp_signed_file, $pdf_path);
            $this->clearTempFiles($pdf_path);
            return $pdf_path;
        }

        // Aufräumen bei Fehler
        if (file_exists($temp_file)) {
            unlink($temp_file);
        }
        if (file_exists($pdf_path)) {
            unlink($pdf_path);
        }

        throw new Exception("Fehler beim Signieren: " . $result->errorOutput());
    }

    /**
     * Löscht die Temporären PDF Dateien
     *
     * @param mixed $original_file_path
     * @return void
     */
    public function clearTempFiles($original_file_path)
    {
        $path_without_pdf_extension = str_replace('.pdf', '', $original_file_path);
        $tmp_path = $path_without_pdf_extension . '_tmp.pdf';
        $signed_path = $path_without_pdf_extension . '_signed.pdf';

        if (file_exists($tmp_path)) {
            unlink($tmp_path);
        }

        if (file_exists($signed_path)) {
            unlink($signed_path);
        }
    }
}
