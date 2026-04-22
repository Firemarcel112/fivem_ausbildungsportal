<?php

namespace App\Services\Export;

use App\Interfaces\ExportInterface;
use Spatie\SimpleExcel\SimpleExcelWriter;

abstract class AbstractExportService implements ExportInterface
{
    /**
     * Anzahl der Zeilen, nach denen der Buffer geleert wird. Dies verhindert Memory-Leaks bei großen Datenmengen.
     *
     * @var int
     */
    protected int $chunk_size = 1000;

    /**
     * Exportiert die Daten in Excel
     *
     * @param  iterable<mixed> $data
     * @param  string          $filename
     * @param  string          $file_type
     * @return void
     */
    public function export(iterable $data, string $filename, string $file_type = 'xlsx'): void
    {

        $writer = $this->initializeWriter($filename, $file_type)
            ->addHeader($this->setHeaders());

        $buffer_count = 0;

        foreach ($data as $item) {

            $writer->addRow($this->mapToRow($item));

            $buffer_count++;

            if (++$buffer_count % $this->chunk_size === 0) {
                flush();
            }
        }
    }

    /**
     * Initialisiert den Writer
     *
     * @param string $filename  Name der Datei ohne Endung
     * @param string $file_type Endung der Datei (z.B. xlsx, csv)
     */
    protected function initializeWriter(string $filename, string $file_type): SimpleExcelWriter
    {
        return SimpleExcelWriter::streamDownload("{$filename}.{$file_type}");
    }

    /**
     * @return array<string>
     */
    abstract protected function setHeaders(): array;

    /**
     * @param  mixed                $item
     * @return array<string, mixed>
     */
    abstract protected function mapToRow($item): array;
}
