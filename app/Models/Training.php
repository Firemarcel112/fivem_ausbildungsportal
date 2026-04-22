<?php

namespace App\Models;

use App\Enums\Training\Participant as ParticipantEnum;
use App\Models\Qualifications\Requirement;
use App\Models\Trainings\Participant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * @property int $training_id
 * @property int $trainer_id
 * @property int $qualification_id
 * @property int|null $fraktion_id
 * @property string|null $meeting_point
 * @property string|null $additional_information
 * @property Carbon $date
 * @property Carbon $time
 * @property int $max_participants
 * @property int $min_participants
 * @property int $completed
 * @property int $canceled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read EloquentCollection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property mixed $cancelled
 * @property-read mixed $count_participants
 * @property-read mixed $date_with_time
 * @property-read mixed $full_date_with_time
 * @property-read mixed $name
 * @property-read EloquentCollection<int, Participant> $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\Qualification|null $qualification
 * @property-read EloquentCollection<int, Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read \App\Models\User|null $trainer
 * @property-read mixed $trainer_name
 *
 * @method static Builder<static>|Training isAvailable()
 * @method static Builder<static>|Training isCompleted(bool $completed = true)
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
 *
 * @mixin \Eloquent
 */
class Training extends BaseModel
{
    protected $table = 'trainings';

    protected $primaryKey = 'training_id';

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
    ];

    protected $guarded = ['training_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt Alle verfügbaren Ausbildungen zurück
     *
     * @param  bool                 $show_only_available Zeigt Ausbildungen unabhängig von Gültigkeit/Zeitraum an
     * @return Collection<Training>
     */
    public static function getUpcomingTrainings(bool $show_only_available = true): Collection
    {
        return self::with([
            'trainer.account',
            'qualification',
            'participants.account.fractions',
            'requirements.fraction',
        ])
            ->when($show_only_available, function ($query) {
                $query->isAvailable();
            })
            ->isCompleted(false)
            ->orderByDefault()
            ->get();
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

        return __('general.datum_uhrzeit_readable', [
            'date' => $date,
            'time' => $time,
        ]);
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

        return Carbon::parse($date . ' ' . $time);
    }

    /**
     * Gibt die Teilnehmerliste Sortiert zurück
     *
     * @return EloquentCollection<int, \App\Models\Trainings\Participant>
     */
    public function getSortParticipants(): EloquentCollection
    {
        return $this->participants->load(['account.fractions', 'account.qualifications', 'account.user'])
            ->sortBy('account.first_name');
    }

    /**
     * Prüft ob man sich noch anmelden kann
     *
     * @param  bool $only_time (Berücksichtigt nur die Zeit ohne Teilnehmeranzahl)
     * @return bool
     */
    public function canRegister(int $enroll_deadline_in_minutes = 0)
    {
        $now = now();
        $end = $this->date->copy()->setTimeFromTimeString($this->time->format('H:i'))
            ->subMinutes($enroll_deadline_in_minutes);
        $can_register_time = $now < $end;

        $count_participant = $this->count_participants;
        $max_participants = $this->max_participants;

        $can_register_paricipant = $count_participant < $max_participants;

        if ($max_participants == ParticipantEnum::UNLIMITED->value) {
            $can_register_paricipant = true;
        }

        if (empty($max_participants)) {
            $can_register_paricipant = false;
        }

        return $can_register_time && $can_register_paricipant;
    }

    /**
     * Gibt an ob der Benutzer bereits zur Ausbildung angemeldet ist
     *
     * @return bool
     */
    public function isRegistered(): bool
    {
        $registered = $this->participants->firstWhere('user_id', Auth::user()?->getKey() ?? 0);

        if (!empty($registered)) {
            return true;
        }

        return false;
    }

    public function checkIfTrainerIsInactive(int $user_id)
    {
        return !User::query()
            ->withIsTrainer()
            ->isUserId($user_id)
            ->exists();
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

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Verfügbare Ausbildungen
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
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
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $available
     * @return Builder
     */
    public function scopeIsCompleted(Builder $query, bool $completed = true)
    {
        return $query->where('completed', $completed ? 1 : 0);
    }

    /**
     * Scope für Standardsortierung
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query, string $direction = 'asc')
    {
        return $query->orderBy('date', $direction)
            ->orderBy('time', $direction);
    }

    # ########################
    # RELATIONS
    # ########################

    /**
     * Relation für Ausbilder
     *
     * @return BelongsTo<User>
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'trainer_id',
            'id',
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

    public function participants(): HasMany
    {
        return $this->hasMany(
            Participant::class,
            'training_id',
            'training_id',
        )->with('account');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(
            Requirement::class,
            'qualification_id',
            'qualification_id'
        )->orderByDefault();
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################

    public function countParticipants(): Attribute
    {
        return Attribute::make(function () {
            if (array_key_exists('count_participants', $this->getAttributes())) {
                return (int) $this->count_participants;
            }

            return $this->participants->count();
        });
    }

    public function name(): Attribute
    {
        return Attribute::make(function () {
            return $this->qualification->name;
        });
    }

    public function trainerName(): Attribute
    {
        return Attribute::make(function () {
            if ($this->trainer->isTrainer() || $this->isCompleted()) {
                return $this->trainer->full_name;
            }

            return __('general.unbekannt');
        });
    }

    public function fullDateWithTime(): Attribute
    {
        return Attribute::make(
            get: fn() => str($this->date->format('d.m.Y'))
                ->append(' ')
                ->append($this->time->format('H:i'))
                ->value(),
        );
    }

    public function dateWithTime(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date->copy()->setTimeFrom($this->time),
        );
    }

    /**
     * @return Attribute<bool, bool>
     */
    public function cancelled(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->canceled,
            set: fn($value) => [
                'canceled' => $value,
            ],
        );
    }
}
