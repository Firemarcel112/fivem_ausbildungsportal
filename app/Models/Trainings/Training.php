<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\Qualifications\Qualification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $training_id
 * @property int $trainer_id
 * @property int $qualification_id
 * @property int|null $fraktion_id
 * @property string|null $meeting_point
 * @property string|null $additional_information
 * @property string $date
 * @property string $time
 * @property int $max_participants
 * @property int $min_participants
 * @property int $completed
 * @property int $canceled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trainings\Participant> $participants
 * @property-read int|null $participants_count
 * @property-read Qualification|null $qualification
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trainings\Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read User|null $trainer
 * @method static Builder<static>|Training isAvailable()
 * @method static Builder<static>|Training isCompletedBuilder(int $available = 1)
 * @method static Builder<static>|Training newModelQuery()
 * @method static Builder<static>|Training newQuery()
 * @method static Builder<static>|Training orderByDefault(string $direction = 'asc')
 * @method static Builder<static>|Training query()
 * @method static Builder<static>|Training whereAdditionalInformation($value)
 * @method static Builder<static>|Training whereCanceled($value)
 * @method static Builder<static>|Training whereCompleted($value)
 * @method static Builder<static>|Training whereCreatedAt($value)
 * @method static Builder<static>|Training whereDate($value)
 * @method static Builder<static>|Training whereFraktionId($value)
 * @method static Builder<static>|Training whereMaxParticipants($value)
 * @method static Builder<static>|Training whereMeetingPoint($value)
 * @method static Builder<static>|Training whereMinParticipants($value)
 * @method static Builder<static>|Training whereQualificationId($value)
 * @method static Builder<static>|Training whereTime($value)
 * @method static Builder<static>|Training whereTrainerId($value)
 * @method static Builder<static>|Training whereTrainingId($value)
 * @method static Builder<static>|Training whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Training extends BaseModel
{
    protected $table = 'trainings';
    protected $primaryKey = 'training_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Gibt den Namen des Ausbilders zurück
     * @return mixed
     */
    public function getTrainerName()
    {
        if ($this->trainer->isTrainer() || $this->isCompleted()) {
            return $this->trainer->getFullName();
        }
        return __('general.unbekannt');
    }

    /**
     * Gibt dass Datum im Lesbaren Format zurück
     *
     * @return string
     */
    public function getFormatedDate()
    {
        $date = $this->getDate()->format('d.m.Y');
        $time = $this->getTime()->format('H:i');

        return  __('general.datum_uhrzeit_readable', [
            'date' => $date,
            'time' => $time,
        ]);
    }

    /**
     * Gibt den Namen der Ausbildung zurück
     *
     * @param $with_link (Gibt den Namen inklusive ID und Anzeigen Link zurück)
     * @param $with_id (Gibt den Namen mit ID zurück)
     */
    public function getName(bool $with_link = false, bool $with_id = false)
    {
        $name = $this->qualification->getName();
        $id = $this->getId();

        $return = $name;

        if ($with_link) {
            $return = '<a class="text-secondary" href="' . route('ausbildung.show', $this) . '">' . $name . ' - ' . __('general.lehrgangs_id') . ': ' . $id . '</a>';
        }

        if ($with_id && !$with_link) {
            $return .= ' - ' . __('general.lehrgangs_id') . ': ' . $id;
        }

        return $return;
    }

    /**
     * Gibt die Anzahl der Teilnehmer zurück
     */
    public function getCountParticipants()
    {
        $this->load('participants');
        return $this->participants->count();
    }

    /**
     * Gibt die Zeit für den Lehrgang zurück
     *
     * @return Carbon
     */
    public function getDeadlineTime(): Carbon
    {
        $time = $this->getTime()->format('H:i:00');
        $date = $this->getDate()->format('y-m-d');

        return Carbon::parse($date . ' '  . $time);
    }

    /**
     * Gibt minimale und Maximale Teilnehmer aus
     * @return string
     */
    public function getOutputCountAllowedParticipants()
    {
        $max = $this->getMaxParticipants();
        if ($max == -1) {
            $max = __('general.unbegrenzt');
        }

        return $this->participants->count() . ' / ' . $max;
    }

    /**
     * Gibt die Teilnehmerliste Sortiert zurück
     *
     * @return mixed
     */
    public function getSortParticipants(): mixed
    {
        return $this->participants->load(['account.fractions', 'account.qualifications'])
            ->sortBy('account.first_name');
    }

    /**
     * Prüft ob man sich noch anmelden kann
     *
     * @param bool $only_time (Berücksichtigt nur die Zeit ohne Teilnehmeranzahl)
     * @return bool
     */
    public function canRegister(bool $only_time = false)
    {
        $now = now();
        $end = $this->getDeadlineTime()->subMinutes(config('app.enroll_deadline'));
        $can_register_time = $now < $end;

        $count_participant = $this->getCountParticipants();
        $max_participants = $this->getMaxParticipants();

        $can_register_paricipant = $count_participant < $max_participants;

        if ($max_participants == -1) {
            $can_register_paricipant = true;
        }

        if ($max_participants == 0) {
            $can_register_paricipant = false;
        }

        if ($only_time) {
            return $can_register_time;
        }
        return $can_register_time && $can_register_paricipant;
    }

    /**
     * Gibt an ob der Benutzer bereits zur Ausbildung angemeldet ist
     * @return bool
     */
    public function isRegistered(): bool
    {
        $registered = $this->participants?->firstWhere('user_id', Auth::user()?->getId() ?? 0);

        if (!empty($registered)) {
            return true;
        }
        return false;
    }

    /**
     * Zeigt an ob eine Ausbildung bereits abgeschlossen ist
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed == 1;
    }

    /**
     * Gibt eine Warnung zurück wenn der Ausbilder nicht mehr Aktiv ist
     *
     * @return string|null
     */
    public function getWarningInactiveTrainer()
    {
        $trainers = User::getTrainers()
            ->pluck('id')
            ->toArray();
        if (!in_array($this->getTrainerId(), $trainers)) {
            $inactive_trainer = User::find($this->getTrainerId());

            $trainer_name = $inactive_trainer?->getFullName() ?? null;

            if (!$trainer_name) {
                $trainer_name = $this->getTrainerId();
            }

            return $trainer_name . ' - ' . __('general.ausbilder_nicht_mehr_aktiv');
        }
        return null;
    }

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Verfügbare Ausbildungen
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeIsAvailable(Builder $query)
    {
        return $query->where(function ($query) {
            $query->where('date', '>', now()->format('Y-m-d'))
                ->orWhere(function ($query) {
                    $query->where('date', '=', now()->format('Y-m-d'))
                        ->where('time', '>=', now()->format('H:i:s'));
                });
        });
    }

    /**
     * Scope für Abgeschlossene Ausbildung
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $available
     * @return Builder
     */
    public function scopeIsCompletedBuilder(Builder $query, int $available = 1)
    {
        return $query->where('completed', $available);
    }

    /**
     * Scope für Standardsortierung
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy('date', $direction)
            ->orderBy('time', $direction);
    }

    #########################
    # RELATIONS
    #########################

    public function trainer(): HasOne
    {
        return $this->hasOne(
            User::class,
            'id',
            'trainer_id'
        );
    }

    public function qualification(): BelongsTo
    {
        return $this->belongsTo(
            Qualification::class,
            'qualification_id',
            'qualification_id',
        );
    }

    public function participants()
    {
        return $this->hasMany(
            Participant::class,
            'training_id',
            'training_id',
        )->with('account');
    }

    public function requirements()
    {
        return $this->hasMany(
            Requirement::class,
            'qualification_id',
            'qualification_id'
        )->orderByDefault();
    }

    #########################
    # GET & SET
    #########################

    /**
     * Get the training_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->training_id;
    }

    /**
     * Set the training_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->training_id = $value;
    }

    /**
     * Get the training_id attribute.
     *
     * @return int
     */
    public function getTrainingId(): int
    {
        return $this->training_id;
    }

    /**
     * Set the training_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setTrainingId(int $value)
    {
        $this->training_id = $value;
    }

    /**
     * Get the trainer_id attribute.
     *
     * @return int
     */
    public function getTrainerId(): int
    {
        return $this->trainer_id;
    }

    /**
     * Set the trainer_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setTrainerId(int $value)
    {
        $this->trainer_id = $value;
    }

    /**
     * Get the qualification_id attribute.
     *
     * @return int
     */
    public function getQualificationId(): int
    {
        return $this->qualification_id;
    }

    /**
     * Set the qualification_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setQualificationId(int $value)
    {
        $this->qualification_id = $value;
    }

    /**
     * Get the fraktion_id attribute.
     *
     * @return ?int
     */
    public function getFraktionId(): ?int
    {
        return $this->fraktion_id;
    }

    /**
     * Set the fraktion_id attribute.
     *
     * @param ?int $value
     * @return void
     */
    public function setFraktionId(?int $value)
    {
        $this->fraktion_id = $value;
    }

    /**
     * Get the meeting_point attribute.
     *
     * @return ?string
     */
    public function getMeetingPoint(): ?string
    {
        return $this->meeting_point;
    }

    /**
     * Set the meeting_point attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setMeetingPoint(?string $value)
    {
        $this->meeting_point = $value;
    }

    /**
     * Get the additional_information attribute.
     *
     * @return ?string
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additional_information;
    }

    /**
     * Set the additional_information attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setAdditionalInformation(?string $value)
    {
        $this->additional_information = $value;
    }

    /**
     * Get the date attribute.
     *
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return Carbon::parse($this->date);
    }

    /**
     * Set the date attribute.
     *
     * @param Carbon $value
     * @return void
     */
    public function setDate(Carbon $value)
    {
        $this->date = $value;
    }

    /**
     * Get the time attribute.
     *
     * @return Carbon
     */
    public function getTime(): Carbon
    {
        return Carbon::parse($this->time);
    }

    /**
     * Set the time attribute.
     *
     * @param Carbon $value
     * @return void
     */
    public function setTime(Carbon $value)
    {
        $this->time = $value;
    }

    /**
     * Get the max_participants attribute.
     *
     * @return int
     */
    public function getMaxParticipants(): int
    {
        return $this->max_participants;
    }

    /**
     * Set the max_participants attribute.
     *
     * @param int $value
     * @return void
     */
    public function setMaxParticipants(int $value)
    {
        $this->max_participants = $value;
    }

    /**
     * Get the min_participants attribute.
     *
     * @return int
     */
    public function getMinParticipants(): int
    {
        return $this->min_participants;
    }

    /**
     * Set the min_participants attribute.
     *
     * @param int $value
     * @return void
     */
    public function setMinParticipants(int $value)
    {
        $this->min_participants = $value;
    }

    /**
     * Get the completed attribute.
     *
     * @return int
     */
    public function getCompleted(): int
    {
        return $this->completed;
    }

    /**
     * Set the completed attribute.
     *
     * @param int $value
     * @return void
     */
    public function setCompleted(int $value)
    {
        $this->completed = $value;
    }

    /**
     * Get the canceled attribute.
     *
     * @return int
     */
    public function getCanceled(): int
    {
        return $this->canceled;
    }

    /**
     * Set the canceled attribute.
     *
     * @param int $value
     * @return void
     */
    public function setCanceled(int $value)
    {
        $this->canceled = $value;
    }

    /**
     * Get the created_at attribute.
     *
     * @return ?Carbon
     */
    public function getCreated(): ?Carbon
    {
        return is_null($this->created_at) ? null : Carbon::parse($this->created_at);
    }

    /**
     * Set the created_at attribute.
     *
     * @param ?Carbon $value
     * @return void
     */
    public function setCreated(?Carbon $value)
    {
        $this->created_at = $value;
    }

    /**
     * Get the updated_at attribute.
     *
     * @return ?Carbon
     */
    public function getUpdated(): ?Carbon
    {
        return is_null($this->updated_at) ? null : Carbon::parse($this->updated_at);
    }

    /**
     * Set the updated_at attribute.
     *
     * @param ?Carbon $value
     * @return void
     */
    public function setUpdated(?Carbon $value)
    {
        $this->updated_at = $value;
    }
}
