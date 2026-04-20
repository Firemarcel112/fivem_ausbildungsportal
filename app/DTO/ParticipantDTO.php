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

    public Carbon $date_of_birth;

    public ?string $birth_location;

    public ?string $notices;

    public string $full_name = '';

    /**
     * ParticipantDTO constructor.
     *
     * @param string $salutation     Anrede
     * @param string $first_name     Vorname
     * @param string $last_name      Nachname
     * @param Carbon $date_of_birth     Geburtsdatum
     * @param string $birth_location Geburtsort
     */
    public function __construct(
        string $salutation,
        string $first_name,
        string $last_name,
        Carbon $date_of_birth,
        ?string $birth_location,
        ?string $notices = null,
    ) {
        $this->salutation = $salutation;
        $this->first_name = $first_name;
        $this->last_name = $last_name;
        $this->date_of_birth = $date_of_birth;
        $this->birth_location = $birth_location;
        $this->notices = $notices;
        $this->full_name = $this->first_name . ' ' . $this->last_name;
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
