<?php

namespace App\Models\Qualifications;

use App\Models\BaseModel;
use App\Models\Trainings\Requirement;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $qualification_id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder<static>|Qualification isOrderByDefault()
 * @method static Builder<static>|Qualification newModelQuery()
 * @method static Builder<static>|Qualification newQuery()
 * @method static Builder<static>|Qualification query()
 * @method static Builder<static>|Qualification whereCreatedAt($value)
 * @method static Builder<static>|Qualification whereName($value)
 * @method static Builder<static>|Qualification whereQualificationId($value)
 * @method static Builder<static>|Qualification whereRank($value)
 * @method static Builder<static>|Qualification whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Requirement> $requirements
 * @property-read int|null $requirements_count
 * @mixin \Eloquent
 */
class Qualification extends BaseModel
{
    protected $table = 'qualifications';
    protected $primaryKey = 'qualification_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Standardsortierung
     * @param Builder $query
     * @return Builder
     */
    public function scopeIsOrderByDefault(Builder $query)
    {
        return $query->orderBy('rank')
            ->orderBy('name');
    }

    #########################
    # RELATIONS
    ########################

    public function requirements()
    {
        return $this->hasMany(
            Requirement::class,
            'qualification_id',
            'qualification_id'
        )
            ->orderByDefault();
    }

    #########################
    # GET & SET
    #########################

    /**
     * Get the qualification_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->qualification_id;
    }

    /**
     * Set the qualification_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->qualification_id = $value;
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
