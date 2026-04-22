<?php

namespace App\DTO;

use App\Enums\Training\Participant as ParticipantEnum;
use App\Models\Qualifications\Requirement;
use App\Models\Training;
use App\Models\Trainings\Participant;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TrainingViewData
{
    public string $day_of_week;

    public string $date_output;

    public string $time_output;

    public string $full_time;

    /**
     * Summary of __construct
     *
     * @param int                                          $id
     * @param Carbon                                       $date
     * @param Carbon                                       $time
     * @param string                                       $name
     * @param int                                          $count_participants
     * @param int                                          $min_participants
     * @param int                                          $max_participants
     * @param string                                       $trainer_name
     * @param string                                       $meeting_point
     * @param ?string                                      $additional_informations,
     * @param array<string, SimpleListItem>                $requirements
     * @param array<iterable, TrainingParticipantViewData> $participants
     * @param bool                                         $can_register
     */
    public function __construct(
        public int $id,
        public Carbon $date,
        public Carbon $time,
        public string $name,
        public int $count_participants,
        public int $min_participants,
        public int $max_participants,
        public int $trainer_id,
        public string $trainer_name,
        public string $meeting_point,
        public ?string $additional_informations,
        public array $requirements = [],
        public array $participants = [],
        public bool $can_register = true,
        public bool $is_completed = false,
        public bool $trainer_is_inactive = false,
        public bool $can_completed = false,
        public bool $can_generate_certificate = true,
        public ?string $trainer_url = null,
        public bool $can_show_participants = false,
        public bool $can_delete = false,
    ) {
        $this->day_of_week = __('general.tag.' . $date->format('N'));
        $this->date_output = $this->date->format('d.m.Y');
        $this->time_output = $this->time->format('H:i');
        $this->full_time = str($this->date_output)
            ->append(' ')
            ->append($this->time_output)
            ->value();
    }

    /**
     * gibt zurück ob zu wenig Teilnehmer angemeldet sind
     *
     * @return bool
     */
    public function tooFewParticipants(): bool
    {
        return $this->count_participants < $this->min_participants;
    }

    /**
     * Gibt zurück ob der User bereits Angemeldet ist
     *
     * @param  int  $user_id
     * @return bool
     */
    public function isRegistered(int $user_id = 0): bool
    {
        $participants = collect($this->participants);

        return $participants->contains('user_id', $user_id);
    }

    /**
     * Gibt zurück ob die Anmeldefrist schon abgelaufen ist
     *
     * @param  int  $enroll_deadline_in_minutes
     * @return bool
     */
    public function canEnrollWarning(int $enroll_deadline_in_minutes = 0)
    {

        $date_time = $this->date->copy()->setTimeFrom($this->time)
            ->subMinutes($enroll_deadline_in_minutes);

        return now()->greaterThan($date_time);
    }

    public function getOutputCountAllowedParticipants()
    {
        $max = $this->max_participants;
        if ($max == ParticipantEnum::UNLIMITED->value) {
            $max = __('general.unbegrenzt');
        }

        return $this->count_participants . ' / ' . $max;
    }

    public static function fromModel(
        Training $training,
        int $enroll_deadline_in_minutes = 0,
        bool $force_url = false,
    ) {

        $training->loadMissing([
            'participants.account.fractions',
            'requirements.fraction',
            'trainer.account',
            'trainer.permissions',
            'trainer.roles',
            'qualification',
        ]);

        $url = null;

        $auth_user = Auth::user();

        if ($auth_user?->can('view', $training) || $force_url) {
            $url = route('ausbildung.show', $training->getKey());
        }

        $can_generate_certificate = $training->qualification->generate_certificate;

        $can_completed = now()->addMinutes((int) config('settings.training_complete_minutes')) > $training->date_with_time;

        $can_delete = $auth_user?->can('delete', $training) ?? false;

        $can_show_participants = $auth_user?->can('showParticipants', $training) ?? false;

        return new self(
            $training->getKey(),
            $training->date,
            $training->time,
            $training->name,
            $training->count_participants,
            $training->min_participants,
            $training->max_participants,
            $training->trainer_id,
            $training->trainer_name,
            $training->meeting_point,
            $training->additional_information,
            $training->requirements
                ->map(fn(Requirement $requirement) => new SimpleListItem(
                    $requirement->getKey(),
                    $requirement->name,
                    $requirement->fraction->name,
                ))->groupBy('description')
                ->toArray(),
            $training->participants->sortBy('account.first_name')->map(fn(Participant $participant) => TrainingParticipantViewData::fromModel($participant, $training))->toArray(),
            $training->canRegister($enroll_deadline_in_minutes),
            $training->completed,
            $training->checkIfTrainerIsInactive($training->trainer_id),
            $can_completed,
            $can_generate_certificate,
            $url,
            $can_show_participants,
            $can_delete,
        );
    }

    public function getName()
    {
        if (Auth::user()?->isTrainer()) {
            return str($this->name)
                ->append(': ')
                ->append(__('general.lehrgangs_id'))
                ->append(' ')
                ->append($this->id)
                ->value();
        }

        return $this->name;
    }
}
