<?php

namespace App\Interfaces;

interface ExportInterface
{
    /** @phpstan-ignore-next-line */
    public function export(iterable $data, string $filename): void;
}
