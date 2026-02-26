<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Carbon\Carbon;
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
    # GET & SET
    # ########################

    /**
     * Get the user_fraction_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->user_fraction_id;
    }

    /**
     * Set the user_fraction_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->user_fraction_id = $value;
    }

    /**
     * Get the user_fraction_id attribute.
     *
     * @return int
     */
    public function getUserFractionId(): int
    {
        return $this->user_fraction_id;
    }

    /**
     * Set the user_fraction_id attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setUserFractionId(int $value)
    {
        $this->user_fraction_id = $value;
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
     * @param  int  $value
     * @return void
     */
    public function setFractionId(int $value)
    {
        $this->fraction_id = $value;
    }

    /**
     * Get the default attribute.
     *
     * @return int
     */
    public function getDefault(): int
    {
        return $this->default;
    }

    /**
     * Set the default attribute.
     *
     * @param  int  $value
     * @return void
     */
    public function setDefault(int $value)
    {
        $this->default = $value;
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
