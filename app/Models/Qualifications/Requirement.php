<?php

namespace App\Models\Qualifications;

use App\Models\BaseModel;
use App\Models\Fractions\Fraction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $requirement_id
 * @property int $qualification_id
 * @property int $fraktion_id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder<static>|Requirement isOrderByDefault()
 * @method static Builder<static>|Requirement newModelQuery()
 * @method static Builder<static>|Requirement newQuery()
 * @method static Builder<static>|Requirement query()
 * @method static Builder<static>|Requirement whereCreatedAt($value)
 * @method static Builder<static>|Requirement whereFraktionId($value)
 * @method static Builder<static>|Requirement whereName($value)
 * @method static Builder<static>|Requirement whereQualificationId($value)
 * @method static Builder<static>|Requirement whereRank($value)
 * @method static Builder<static>|Requirement whereRequirementId($value)
 * @method static Builder<static>|Requirement whereUpdatedAt($value)
 * @property int $fraction_id
 * @method static Builder<static>|Requirement orderByDefault()
 * @method static Builder<static>|Requirement whereFractionId($value)
 * @property-read Fraction|null $fraction
 * @mixin \Eloquent
 */
class Requirement extends BaseModel
{
    protected $table = 'requirements';
    protected $primaryKey = 'requirement_id';

    protected $guarded = ['requirement_id'];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    /**
     * Legt die Standardsortierung fest
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query)
    {
        return $query->orderBy('rank');
    }

    #########################
    # RELATIONS
    #########################

    public function fraction()
    {
        return $this->belongsTo(
            Fraction::class,
            'fraction_id',
            'fraction_id'
        );
    }

    #########################
    # GET & SET
    #########################

    /**
     * Get the requirement_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->requirement_id;
    }

    /**
     * Set the requirement_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->requirement_id = $value;
    }

    /**
     * Get the requirement_id attribute.
     *
     * @return int
     */
    public function getRequirementId(): int
    {
        return $this->requirement_id;
    }

    /**
     * Set the requirement_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setRequirementId(int $value)
    {
        $this->requirement_id = $value;
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
     * Get the fraction_id attribute.
     *
     * @return int
     */
    public function getFractionId(): int
    {
        return $this->fraction_id;
    }

    /**
     * Set the fraction_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setFractionId(int $value)
    {
        $this->fraction_id = $value;
    }

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set the name attribute.
     *
     * @param string $value
     * @return void
     */
    public function setName(string $value)
    {
        $this->name = $value;
    }

    /**
     * Get the rank attribute.
     *
     * @return int
     */
    public function getRank(): int
    {
        return $this->rank;
    }

    /**
     * Set the rank attribute.
     *
     * @param int $value
     * @return void
     */
    public function setRank(int $value)
    {
        $this->rank = $value;
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
