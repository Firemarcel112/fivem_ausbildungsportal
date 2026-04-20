<?php

namespace App\DTO;

use App\Enums\Gender;
use App\Enums\ParticipantStatus;
use App\Models\Training;
use App\Models\Trainings\Participant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

readonly class TrainingParticipantViewData
{
    /**
     * @param int                             $id
     * @param int                             $account_id
     * @param int                             $user_id
     * @param string                          $salutation
     * @param string                          $full_name
     * @param string                          $first_name
     * @param string                          $last_name
     * @param Gender                          $gender
     * @param Carbon                          $date_of_birth
     * @param mixed                           $birth_location
     * @param mixed                           $notices
     * @param string                          $sign_at
     * @param string                          $default_fraction_short_name
     * @param Collection<int, SimpleListItem> $qualification_names
     * @param mixed                           $status
     */
    public function __construct(
        public int $id,
        public int $account_id,
        public int $user_id,
        public string $salutation,
        public string $full_name,
        public string $first_name,
        public string $last_name,
        public Gender $gender,
        public Carbon $date_of_birth,
        public ?string $birth_location,
        public ?string $notices,
        public string $sign_at,
        public string $default_fraction_short_name,
        public Collection $qualification_names,
        public ?ParticipantStatus $status = null,
        public bool $can_view_profile = true,

    ) {}

    public static function fromModel(Participant $participant, ?Training $training): self
    {
        $participant->loadMissing('account.fractions');

        $qualification_names = $participant->account?->qualifications->pluck('name')
            ->map(fn(string $item) => new SimpleListItem(
                0,
                $item,
            ));

        $can_view_profile = Auth::user()->can('view', $participant->account->user);

        return new self(
            $participant->getKey(),
            $participant->account->getKey(),
            $participant->account->user->getKey(),
            $participant->account->salutation,
            $participant->account->full_name,
            $participant->account->first_name,
            $participant->account->last_name,
            $participant->account->gender,
            $participant->account->date_of_birth,
            $participant->account->birth_location ?? __('general.unbekannt'),
            $participant->notices,
            $participant->created_at->format('d.m.Y H:i'),
            $participant->account->getDefaultFraction()->short_name,
            $qualification_names,
            $participant->getStatus(),
            $can_view_profile,
        );
    }
}
