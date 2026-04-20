<?php

namespace App\DTO;

use App\Models\User;
use App\Models\User\Account;

class TrainerDTO
{
    public string $salutation;

    public string $first_name;

    public string $last_name;

    /**
     * TrainerDTO constructor.
     *
     * @param string $salutation Anrede
     * @param string $first_name Vorname
     * @param string $last_name  Nachname
     */
    public function __construct(
        string $salutation,
        string $first_name,
        string $last_name,
    ) {
        $this->salutation = $salutation;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
    }

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Erstellt eine TrainerDTO aus einem User Modell.
     *
     * @param  User       $trainer
     * @return TrainerDTO
     */
    public static function fromModel(Account|User $trainer)
    {
        if ($trainer instanceof User) {
            $trainer = $trainer->account;
        }

        return new self(
            $trainer->salutation,
            $trainer->first_name,
            $trainer->last_name,
        );
    }
}
