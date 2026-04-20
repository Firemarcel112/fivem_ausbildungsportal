<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $user_qualification_id
 * @property int $user_id
 * @property int $qualification_id
 * @property int|null $training_id
 * @property string|null $date_obtained Erforderlich wenn keine Ausbildung vorliegt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 *
 * @method static Builder<static>|Qualification isQualificationId(int $qualification_id)
 * @method static Builder<static>|Qualification newModelQuery()
 * @method static Builder<static>|Qualification newQuery()
 * @method static Builder<static>|Qualification query()
 * @method static Builder<static>|Qualification whereCreatedAt($value)
 * @method static Builder<static>|Qualification whereDateObtained($value)
 * @method static Builder<static>|Qualification whereQualificationId($value)
 * @method static Builder<static>|Qualification whereTrainingId($value)
 * @method static Builder<static>|Qualification whereUpdatedAt($value)
 * @method static Builder<static>|Qualification whereUserId($value)
 * @method static Builder<static>|Qualification whereUserQualificationId($value)
 *
 * @mixin \Eloquent
 */
class Qualification extends BaseModel
{
    protected $table = 'user_qualifications';

    protected $primaryKey = 'user_qualification_id';

    protected $guarded = ['user_qualification_id'];

    protected $casts = [
        'created_at' => 'date',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Qualifikation-ID
     *
     * @param  Builder<Qualification> $query
     * @param  int                    $qualification_id
     * @return Builder<Qualification>
     */
    public function scopeIsQualificationId(Builder $query, int $qualification_id): Builder
    {
        return $query->where('qualification_id', $qualification_id);
    }

    # ########################
    # RELATIONS
    # ########################

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
