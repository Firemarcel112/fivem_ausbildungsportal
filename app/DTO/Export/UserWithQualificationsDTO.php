<?php

namespace App\DTO\Export;

use App\Models\User\Account;

class UserWithQualificationsDTO
{
    public int $id;

    public string $name;

    public array $qualifications;

    public string $fraction_name;

    public function __construct(
        int $id,
        string $name,
        string $fraction_name,
        array $qualifications
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->fraction_name = $fraction_name;
        $this->qualifications = $qualifications;
    }

    /**
     * DTO anhand von Model zusammenbauen
     *
     * @param  Account                   $account
     * @param  array                     $qualifications
     * @return UserWithQualificationsDTO
     */
    public static function fromModel(Account $account, array $qualifications): self
    {
        $user_qualifications = [];

        foreach ($qualifications as $qualification) {
            $user_qualifications[$qualification] = $account->qualifications->contains('name', $qualification);
        }

        return new self(
            $account->getKey(),
            $account->getFullName(),
            $account->getDefaultFraction()->full_name,
            $user_qualifications
        );
    }
}
