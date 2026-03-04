<?php

namespace App\Services\Export;

use App\DTO\Export\UserWithQualificationsDTO;

class UserWithQualificationsService extends AbstractExportService
{
    public array $qualifications;

    public function __construct(array $qualifications)
    {
        $this->qualifications = $qualifications;
    }

    protected function setHeaders(): array
    {
        return [
            'id' => 'ID',
            'name' => __('general.name'),
            'fraction' => __('general.fraktion'),
            ...$this->qualifications,
        ];
    }

    /**
     * Mappt die Felder des DTOs auf die Spalten der Excel-Datei
     *
     * @param  UserWithQualificationsDTO          $item
     * @return array|array{id: int, name: string}
     */
    protected function mapToRow($item): array
    {
        $row = [
            'id' => $item->id,
            'name' => $item->name,
            'fraction' => $item->fraction_name,
        ];
        foreach ($item->qualifications as $key => $value) {
            $row[$key] = $value;
        }

        return $row;
    }
}
