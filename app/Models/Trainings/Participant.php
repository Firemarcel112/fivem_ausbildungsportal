<?php

namespace App\Models\Trainings;

use App\Enums\ParticipantStatus;
use App\Models\BaseModel;
use App\Models\Training;
use App\Models\User\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
 * @property-read mixed $full_name
 * @property-read Training|null $training
 *
 * @method static Builder<static>|Participant isAccountId(int $user_id)
 * @method static Builder<static>|Participant isTrainingId(int $training_id)
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

    protected $casts = [
        'created_at' => 'datetime',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Abmeldung vom Teilnehmer
     *
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

    public function getStatus(): ParticipantStatus
    {
        return match (true) {
            ((bool) $this->present && (bool) $this->passed) || (bool) $this->passed => ParticipantStatus::PASSED,
            (bool) $this->present => ParticipantStatus::ONLY_PRESENT,
            (bool) $this->logged_out => ParticipantStatus::LOGGED_OUT,
            default => ParticipantStatus::ABSENCE,
        };
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
     * Scope für AccountID
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  int                                   $user_id
     * @return Builder
     */
    public function scopeIsAccountId(Builder $query, int $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    # ########################
    # RELATIONS
    # ########################

    public function account(): HasOne
    {
        return $this->hasOne(
            Account::class,
            'user_account_id',
            'user_id',
        );
    }

    public function training(): HasOne
    {
        return $this->hasOne(
            Training::class,
            'training_id',
            'training_id',
        );
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->account->full_name;
            }
        );
    }
}
