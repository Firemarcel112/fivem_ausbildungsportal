<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $training_ban_id
 * @property int $user_id
 * @property string $date_from
 * @property string $date_to
 * @property string $reason
 * @property int $issuer_id
 * @property string|null $internal_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $issuer
 *
 * @method static Builder<static>|TrainingBan isValid()
 * @method static Builder<static>|TrainingBan newModelQuery()
 * @method static Builder<static>|TrainingBan newQuery()
 * @method static Builder<static>|TrainingBan query()
 * @method static Builder<static>|TrainingBan whereCreatedAt($value)
 * @method static Builder<static>|TrainingBan whereDateFrom($value)
 * @method static Builder<static>|TrainingBan whereDateTo($value)
 * @method static Builder<static>|TrainingBan whereInternalNote($value)
 * @method static Builder<static>|TrainingBan whereIssuerId($value)
 * @method static Builder<static>|TrainingBan whereReason($value)
 * @method static Builder<static>|TrainingBan whereTrainingBanId($value)
 * @method static Builder<static>|TrainingBan whereUpdatedAt($value)
 * @method static Builder<static>|TrainingBan whereUserId($value)
 *
 * @mixin \Eloquent
 */
class TrainingBan extends BaseModel
{
    protected $table = 'training_bans';

    protected $primaryKey = 'training_ban_id';

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt den Namen des Ausstellers zurück
     *
     * @return string
     */
    public function getIssuerName()
    {
        return $this->issuer->account->getFullName();
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Gültigkeit
     *
     * @param  \Illuminate\Database\Eloquent\Builder   $query
     * @return Builder<TrainingBan>|Builder<\Eloquent>
     */
    public function scopeIsValid(Builder $query)
    {
        return $this->where('date_from', '<=', date('Y-m-d'))
            ->where('date_to', '>=', date('Y-m-d'));
    }

    # ########################
    # RELATIONS
    # ########################

    public function issuer()
    {
        return $this->hasOne(
            User::class,
            'id',
            'issuer_id'
        )
            ->with('account');
    }

    # ########################
    # GET & SET
    # ########################

    /**
     * Get the training_ban_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->training_ban_id;
    }

    /**
     * Set the training_ban_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->training_ban_id = $value;
    }

    /**
     * Get the training_ban_id attribute.
     *
     * @return int
     */
    public function getTrainingBanId(): int
    {
        return $this->training_ban_id;
    }

    /**
     * Set the training_ban_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setTrainingBanId(int $value)
    {
        $this->training_ban_id = $value;
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
     * Get the date_from attribute.
     *
     * @return Carbon
     */
    public function getDateFrom(): Carbon
    {
        return Carbon::parse($this->date_from);
    }

    /**
     * Set the date_from attribute.
     *
     * @param  Carbon $value
     * @return void
     */
    public function setDateFrom(Carbon $value)
    {
        $this->date_from = $value;
    }

    /**
     * Get the date_to attribute.
     *
     * @return Carbon
     */
    public function getDateTo(): Carbon
    {
        return Carbon::parse($this->date_to);
    }

    /**
     * Set the date_to attribute.
     *
     * @param  Carbon $value
     * @return void
     */
    public function setDateTo(Carbon $value)
    {
        $this->date_to = $value;
    }

    /**
     * Get the reason attribute.
     *
     * @return string
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Set the reason attribute.
     *
     * @param  string $value
     * @return void
     */
    public function setReason(string $value)
    {
        $this->reason = $value;
    }

    /**
     * Get the issuer_id attribute.
     *
     * @return int
     */
    public function getIssuerId(): int
    {
        return $this->issuer_id;
    }

    /**
     * Set the issuer_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setIssuerId(int $value)
    {
        $this->issuer_id = $value;
    }

    /**
     * Get the internal_note attribute.
     *
     * @return ?string
     */
    public function getInternalNote(): ?string
    {
        return $this->internal_note;
    }

    /**
     * Set the internal_note attribute.
     *
     * @param  ?string $value
     * @return void
     */
    public function setInternalNote(?string $value)
    {
        $this->internal_note = $value;
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
