<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\User\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $training_participant_id
 * @property int $training_id
 * @property int $user_id
 * @property int $present
 * @property int $logged_out
 * @property int $passed
 * @property string|null $notices
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Trainings\Training|null $training
 *
 * @method static Builder<static>|Participant isTrainingId(int $training_id)
 * @method static Builder<static>|Participant isUserId(int $user_id)
 * @method static Builder<static>|Participant newModelQuery()
 * @method static Builder<static>|Participant newQuery()
 * @method static Builder<static>|Participant query()
 * @method static Builder<static>|Participant whereCreatedAt($value)
 * @method static Builder<static>|Participant whereLoggedOut($value)
 * @method static Builder<static>|Participant whereNotices($value)
 * @method static Builder<static>|Participant wherePassed($value)
 * @method static Builder<static>|Participant wherePresent($value)
 * @method static Builder<static>|Participant whereTrainingId($value)
 * @method static Builder<static>|Participant whereTrainingParticipantId($value)
 * @method static Builder<static>|Participant whereUpdatedAt($value)
 * @method static Builder<static>|Participant whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Participant extends BaseModel
{
    protected $table = 'training_participants';

    protected $primaryKey = 'training_participant_id';

    protected $guarded = ['training_participant_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt den vollen Namen zurück
     */
    public function getFullName()
    {
        return $this->account->getFullName();
    }

    public function getBirthDate()
    {
        return $this->account->getDateofBirth()->format('d.m.Y');
    }

    /**
     * Abmeldung vom Teilnehmer
     *
     * @param  string $notice
     * @return void
     */
    public function signOut()
    {
        $this->delete();
    }

    /**
     * Abmeldung vom Teilnehmer
     *
     * @param  string $notice
     * @return void
     */
    public function signOutAdmin(string $notice)
    {
        $this->setLoggedOut(1);
        $this->setNotices($notice);
        $this->save();
    }

    /**
     * Gibt dass Badge zurück
     */
    public function getOutputBadge()
    {
        if ($this->getPresent() && $this->getPassed()) {
            return '<span class="badge bg-success text-white">Bestanden</span>';
        } elseif ($this->getPresent()) {
            return '<span class="badge bg-warning text-white">Nur Anwesend</span>';
        } elseif ($this->getLoggedOut()) {
            return '<span class="badge bg-red text-white">Abgemeldet</span>';
        }

        return '<span class="badge bg-red text-white">Abwesend</span>';
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Ausbildungs ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $training_id
     * @return Builder
     */
    public function scopeisTrainingId(Builder $query, int $training_id)
    {
        return $query->where('training_id', $training_id);
    }

    /**
     * Scope für User ID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $user_id
     * @return Builder
     */
    public function scopeIsUserId(Builder $query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    # ########################
    # RELATIONS
    # ########################

    public function account()
    {
        return $this->hasOne(
            Account::class,
            'user_account_id',
            'user_id',
        );
    }

    public function training()
    {
        return $this->hasOne(
            Training::class,
            'training_id',
            'training_id',
        );
    }

    # ########################
    # GET & SET
    # ########################

    /**
     * Get the training_participant_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->training_participant_id;
    }

    /**
     * Set the training_participant_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->training_participant_id = $value;
    }

    /**
     * Get the training_participant_id attribute.
     *
     * @return int
     */
    public function getTrainingParticipantId(): int
    {
        return $this->training_participant_id;
    }

    /**
     * Set the training_participant_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setTrainingParticipantId(int $value)
    {
        $this->training_participant_id = $value;
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
     * @param  int  $value
     * @return void
     */
    public function setTrainingId(int $value)
    {
        $this->training_id = $value;
    }

    /**
     * Get the user_id attribute.
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Set the user_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setUserId(int $value)
    {
        $this->user_id = $value;
    }

    /**
     * Get the present attribute.
     *
     * @return int
     */
    public function getPresent(): int
    {
        return $this->present;
    }

    /**
     * Set the present attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setPresent(int $value)
    {
        $this->present = $value;
    }

    /**
     * Get the logged_out attribute.
     *
     * @return int
     */
    public function getLoggedOut(): int
    {
        return $this->logged_out;
    }

    /**
     * Set the logged_out attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setLoggedOut(int $value)
    {
        $this->logged_out = $value;
    }

    /**
     * Get the passed attribute.
     *
     * @return int
     */
    public function getPassed(): int
    {
        return $this->passed;
    }

    /**
     * Set the passed attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setPassed(int $value)
    {
        $this->passed = $value;
    }

    /**
     * Get the notices attribute.
     *
     * @return ?string
     */
    public function getNotices(): ?string
    {
        return $this->notices;
    }

    /**
     * Set the notices attribute.
     *
     * @param  ?string $value
     * @return void
     */
    public function setNotices(?string $value)
    {
        $this->notices = $value;
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
     * @param  ?Carbon $value
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
     * @param  ?Carbon $value
     * @return void
     */
    public function setUpdated(?Carbon $value)
    {
        $this->updated_at = $value;
    }
}
