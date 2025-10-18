<?php

namespace App\Models\User;

use App\Models\BaseModel;
use App\Models\Fractions\Fraction;
use App\Models\User\Fraction as UserFraction;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User\Qualification as UserQualification;
use App\Models\Qualifications\Qualification;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $user_account_id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder<static>|Account newModelQuery()
 * @method static Builder<static>|Account newQuery()
 * @method static Builder<static>|Account query()
 * @method static Builder<static>|Account whereCreatedAt($value)
 * @method static Builder<static>|Account whereDateOfBirth($value)
 * @method static Builder<static>|Account whereFirstName($value)
 * @method static Builder<static>|Account whereLastName($value)
 * @method static Builder<static>|Account whereUpdatedAt($value)
 * @method static Builder<static>|Account whereUserAccountId($value)
 * @method static Builder<static>|Account whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Fraction> $fractions
 * @property-read int|null $fractions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @method static Builder<static>|Account isSearch(string $search)
 * @mixin \Eloquent
 */
class Account extends BaseModel
{
    protected $table = 'user_accounts';
    protected $primaryKey = 'user_account_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Gibt den vollen Namen zurück
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Suche
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeIsSearch(Builder $query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "{$search}%")
                ->orWhere('last_name', 'LIKE', "{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["{$search}%"]);
        });
    }

    #########################
    # RELATIONS
    #########################

    public function fractions(): BelongsToMany
    {
        return $this->belongsToMany(
            Fraction::class,
            UserFraction::getTableName(),
            'user_id',
            'fraction_id'
        )->withPivot('created_at', 'default');
    }

    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(
            Qualification::class,
            UserQualification::getTableName(),
            'user_id',
            'qualification_id'
        )->withPivot('created_at', 'training_id');
    }

    #########################
    # GET & SET
    #########################

    /**
     * Gibt die Standard Fraktion zurück
     *
     * @return int
     */
    public function getDefaultFractionId(): int
    {
        return $this->fractions->firstWhere('pivot.default', 1)->getId();
    }

    public function getDefaultFraction()
    {
        return $this->fractions->firstWhere('pivot.default', 1);
    }

    /**
     * Get the user_account_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->user_account_id;
    }

    /**
     * Set the user_account_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->user_account_id = $value;
    }

    /**
     * Get the user_account_id attribute.
     *
     * @return int
     */
    public function getUserAccountId(): int
    {
        return $this->user_account_id;
    }

    /**
     * Set the user_account_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setUserAccountId(int $value)
    {
        $this->user_account_id = $value;
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
     * @param int $value
     * @return void
     */
    public function setUserId(int $value)
    {
        $this->user_id = $value;
    }

    /**
     * Get the first_name attribute.
     *
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->first_name;
    }

    /**
     * Set the first_name attribute.
     *
     * @param string $value
     * @return void
     */
    public function setFirstName(string $value)
    {
        $this->first_name = $value;
    }

    /**
     * Get the last_name attribute.
     *
     * @return string
     */
    public function getLastName(): string
    {
        return $this->last_name;
    }

    /**
     * Set the last_name attribute.
     *
     * @param string $value
     * @return void
     */
    public function setLastName(string $value)
    {
        $this->last_name = $value;
    }

    /**
     * Get the date_of_birth attribute.
     *
     * @return Carbon
     */
    public function getDateOfBirth(): Carbon
    {
        return Carbon::parse($this->date_of_birth);
    }

    /**
     * Set the date_of_birth attribute.
     *
     * @param Carbon $value
     * @return void
     */
    public function setDateOfBirth(Carbon $value)
    {
        $this->date_of_birth = $value;
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
