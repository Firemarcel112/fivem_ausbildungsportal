<?php

namespace App\Interfaces;

interface ExportInterface
{
    public function export(iterable $data, string $filename): void;
}
