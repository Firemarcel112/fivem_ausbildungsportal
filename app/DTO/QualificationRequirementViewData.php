<?php

namespace App\DTO;

use App\Models\Qualifications\Requirement;

readonly class QualificationRequirementViewData
{
    public string $unique_id;

    public function __construct(
        public string $label,
        public string $fraction_name,
        public int $fraction_id,
        public int $qualification_id,
    ) {
        $this->unique_id = $fraction_id . '-' . $qualification_id;
    }

    public static function fromModel(
        Requirement $requirement,
    ) {

        return new self(
            $requirement->name,
            $requirement->fraction->name,
            $requirement->fraction_id,
            $requirement->qualification_id,
        );
    }
}
