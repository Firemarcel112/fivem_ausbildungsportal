<?php

namespace App\Services\Export;

class UserWithQualificationsService extends AbstractExportService
{
    /**
     * @var array<string>
     */
    public array $qualifications;

    /**
     * @param array<string> $qualifications
     */
    public function __construct(array $qualifications)
    {
        $this->qualifications = $qualifications;
    }

    protected function setHeaders(): array
    {
        return array_merge(
            [
                'id' => 'ID',
                'name' => __('general.name'),
                'fraction' => __('general.fraktion'),
            ],
            $this->qualifications
        );
    }

    protected function mapToRow($item): array
    {
        $row = [
            'id' => $item->id,
            'name' => $item->name,
            'fraction' => $item->fraction_name,
        ];

        return array_merge($row, $item->qualifications);
    }
}
