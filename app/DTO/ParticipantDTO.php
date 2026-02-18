<?php

namespace App\DTO;

use App\Models\Trainings\Participant;
use Illuminate\Support\Carbon;

class ParticipantDTO
{

    public string $salutation;
    public string $first_name;
    public string $last_name;
    public Carbon $birth_date;
    public ?string $birth_location;

    /**
     * ParticipantDTO constructor.
     * @param string $salutation Anrede
     * @param string $first_name Vorname
     * @param string $last_name Nachname
     * @param Carbon $birth_date Geburtsdatum
     * @param string $birth_location Geburtsort
     */
    public function __construct(
        string $salutation,
        string $first_name,
        string $last_name,
        Carbon $birth_date,
        ?string $birth_location,
    ) {
        $this->salutation = $salutation;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->birth_date = $birth_date;
        $this->birth_location = $birth_location;
    }

    /**
     * Gibt den vollständigen Namen des Teilnehmers zurück.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     *
     * Gibt das Geburtsdatum im Format "d.m.Y" zurück.
     *
     * @return string
     */
    public function getBirthDate(string $format = 'd.m.Y'): string
    {
        return $this->birth_date->format($format);
    }

    /**
     * Erstellt dass DTO anhand eines Participant Models
     * @param Participant $participant
     * @return ParticipantDTO
     */
    public static function fromModel(Participant $participant): self
    {
        $participant = $participant->account;
        return new self(
            $participant->getSalutation(),
            $participant->getFirstName(),
            $participant->getLastName(),
            $participant->getDateOfBirth(),
            $participant->getBirthLocation(),
        );
    }
}
