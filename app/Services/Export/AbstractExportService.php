<?php

namespace App\Services\Export;

use App\Interface\ExportInterface;
use Spatie\SimpleExcel\SimpleExcelWriter;

abstract class AbstractExportService implements ExportInterface
{
    public function export(iterable $data, string $filename, string $file_type = 'xlsx'): void
    {

        $writer = SimpleExcelWriter::streamDownload($filename . '.' . $file_type)
            ->addHeader($this->setHeaders());

        $buffer_count = 0;

        foreach ($data as $item) {

            $writer->addRow($this->mapToRow($item));

            $buffer_count++;

            if ($buffer_count >= 1000) {
                flush();
                $buffer_count = 0;
            }
        }
    }

    abstract protected function setHeaders(): array;

    abstract protected function mapToRow($item): array;
}
