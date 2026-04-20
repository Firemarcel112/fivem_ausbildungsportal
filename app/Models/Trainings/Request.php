<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $training_request_id
 * @property int|null $user_id
 * @property int $qualification_id
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Qualification|null $qualification
 * @property-read User|null $user
 *
 * @method static Builder<static>|Request isDateInFuture()
 * @method static Builder<static>|Request newModelQuery()
 * @method static Builder<static>|Request newQuery()
 * @method static Builder<static>|Request query()
 * @method static Builder<static>|Request whereCreatedAt($value)
 * @method static Builder<static>|Request whereDate($value)
 * @method static Builder<static>|Request whereQualificationId($value)
 * @method static Builder<static>|Request whereTime($value)
 * @method static Builder<static>|Request whereTrainingRequestId($value)
 * @method static Builder<static>|Request whereUpdatedAt($value)
 * @method static Builder<static>|Request whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Request extends BaseModel
{
    protected $table = 'training_requests';

    protected $primaryKey = 'training_request_id';

    protected $guarded = ['training_request_id'];

    protected $casts = [
        'date' => 'datetime',
        'time' => 'datetime',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Datum in der Zukunft
     *
     * @param  Builder<Request> $query
     * @return Builder<Request>
     */
    public function scopeIsDateInFuture(Builder $query)
    {
        return $query->where('date', '>=', now()->tomorrow()->format('Y-m-d'));
    }

    # ########################
    # RELATIONS
    # ########################

    public function user(): HasOne
    {
        return $this->hasOne(
            User::class,
            'id',
            'user_id'
        );
    }

    public function qualification(): HasOne
    {
        return $this->hasOne(
            Qualification::class,
            'qualification_id',
            'qualification_id'
        );
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
