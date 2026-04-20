<?php

namespace App\DTO;

use App\Models\User\Account;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

/**
 * @deprecated use TrainingParticipantviewData
 */
class ParticipantDTO
{
    public string $salutation;

    public string $first_name;

    public string $last_name;

    public Carbon $birth_date;

    public ?string $birth_location;

    public ?string $notices;

    /**
     * ParticipantDTO constructor.
     *
     * @param string $salutation     Anrede
     * @param string $first_name     Vorname
     * @param string $last_name      Nachname
     * @param Carbon $birth_date     Geburtsdatum
     * @param string $birth_location Geburtsort
     */
    public function __construct(
        string $salutation,
        string $first_name,
        string $last_name,
        Carbon $birth_date,
        ?string $birth_location,
        ?string $notices = null,
    ) {
        $this->salutation = $salutation;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->birth_date = $birth_date;
        $this->birth_location = $birth_location;
        $this->notices = $notices;
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
     *
     * @param  Account        $participant
     * @return ParticipantDTO
     */
    public static function fromModel(Account $participant): self
    {
        return new self(
            $participant->salutation,
            $participant->first_name,
            $participant->last_name,
            $participant->date_of_birth,
            $participant->birth_location,
        );
    }

    public static function fromRequest(FormRequest $request, string $salutation)
    {
        return new self(
            $salutation,
            $request->validated('first_name'),
            $request->validated('last_name'),
            $request->date('date_of_birth'),
            $request->validated('birth_location')
        );
    }
}
