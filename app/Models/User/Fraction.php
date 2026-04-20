<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $user_fraction_id
 * @property int $user_id
 * @property int $fraction_id
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 *
 * @method static Builder<static>|Fraction newModelQuery()
 * @method static Builder<static>|Fraction newQuery()
 * @method static Builder<static>|Fraction query()
 * @method static Builder<static>|Fraction whereCreatedAt($value)
 * @method static Builder<static>|Fraction whereDefault($value)
 * @method static Builder<static>|Fraction whereFractionId($value)
 * @method static Builder<static>|Fraction whereUpdatedAt($value)
 * @method static Builder<static>|Fraction whereUserFractionId($value)
 * @method static Builder<static>|Fraction whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Fraction extends BaseModel
{
    protected $table = 'user_fractions';

    protected $primaryKey = 'user_fraction_id';

    protected $guarded = ['user_fraction_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    # ########################
    # RELATIONS
    # ########################

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
