<?php

namespace App\Interface;

interface ExportInterface
{
    public function export(iterable $data, string $filename): void;
}
