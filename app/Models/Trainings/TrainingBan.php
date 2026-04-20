<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $training_ban_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date_from
 * @property \Illuminate\Support\Carbon $date_to
 * @property string $reason
 * @property int $issuer_id
 * @property string|null $internal_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read User|null $issuer
 * @property-read mixed $issuer_name
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

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

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

    /**
     * @return HasOne<User, $this>
     */
    public function issuer(): HasOne
    {
        return $this->hasOne(
            User::class,
            'id',
            'issuer_id'
        )
            ->with('account');
    }

    # ########################
    # ACCESSORS & MUTATORS
    # #########################

    public function issuerName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->issuer?->full_name ?? __('general.unbekannt');
            },
        );
    }
}
