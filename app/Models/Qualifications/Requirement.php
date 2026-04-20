<?php

namespace App\Models\Qualifications;

use App\Models\BaseModel;
use App\Models\Fraction;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $requirement_id
 * @property int $qualification_id
 * @property int $fraction_id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Fraction|null $fraction
 * @property-read Qualification|null $qualification
 *
 * @method static Builder<static>|Requirement joinOrderedQualifications()
 * @method static Builder<static>|Requirement newModelQuery()
 * @method static Builder<static>|Requirement newQuery()
 * @method static Builder<static>|Requirement orderByDefault()
 * @method static Builder<static>|Requirement query()
 * @method static Builder<static>|Requirement whereCreatedAt($value)
 * @method static Builder<static>|Requirement whereFractionId($value)
 * @method static Builder<static>|Requirement whereName($value)
 * @method static Builder<static>|Requirement whereQualificationId($value)
 * @method static Builder<static>|Requirement whereRank($value)
 * @method static Builder<static>|Requirement whereRequirementId($value)
 * @method static Builder<static>|Requirement whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Requirement extends BaseModel
{
    protected $table = 'requirements';

    protected $primaryKey = 'requirement_id';

    protected $guarded = ['requirement_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Legt die Standardsortierung fest
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query)
    {
        return $query->orderBy('rank');
    }

    /**
     * @param  Builder              $query
     * @return Builder<Requirement>
     */
    public function scopeJoinOrderedQualifications(Builder $query): Builder
    {
        $self_table_name = (new self)->getTable();

        $this->joinQualificationsOnce($query);

        return $query->select($self_table_name . '.*')
            ->orderBy('qualifications.hide')
            ->orderBy('qualifications.rank')
            ->orderBy('qualifications.name');
    }

    public function joinQualificationsOnce(Builder $query)
    {
        $table_name = (new Qualification)->getTable();
        $self_table_name = (new self)->getTable();
        $joins = collect($query->getQuery()->joins);

        if (!$joins->pluck('table')->contains($table_name)) {
            $query->leftJoin(
                $table_name,
                $self_table_name . '.qualification_id',
                '=',
                $table_name . '.qualification_id'
            );
        }
    }

    # ########################
    # RELATIONS
    # ########################

    /**
     * @return BelongsTo<Fraction, Requirement>
     */
    public function fraction(): BelongsTo
    {
        return $this->belongsTo(
            Fraction::class,
            'fraction_id',
            'fraction_id'
        );
    }

    /**
     * @return BelongsTo<Qualification, Requirement>
     */
    public function qualification(): BelongsTo
    {
        return $this->belongsTo(
            Qualification::class,
            'qualification_id',
            'qualification_id'
        );
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
