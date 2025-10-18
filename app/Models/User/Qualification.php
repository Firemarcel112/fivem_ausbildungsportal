<?php

namespace App\Models\User;

use App\Models\BaseModel;
use Carbon\Carbon;
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
 * @mixin \Eloquent
 */
class Qualification extends BaseModel
{
    protected $table = 'user_qualifications';
    protected $primaryKey = 'user_qualification_id';

    protected $guarded = ['user_qualification_id'];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    #########################
    # RELATIONS
    #########################

    #########################
    # GET & SET
    #########################

    /**
     * Get the user_qualification_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->user_qualification_id;
    }

    /**
     * Set the user_qualification_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->user_qualification_id = $value;
    }

    /**
     * Get the user_qualification_id attribute.
     *
     * @return int
     */
    public function getUserQualificationId(): int
    {
        return $this->user_qualification_id;
    }

    /**
     * Set the user_qualification_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setUserQualificationId(int $value)
    {
        $this->user_qualification_id = $value;
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
     * Get the training_id attribute.
     *
     * @return ?int
     */
    public function getTrainingId(): ?int
    {
        return $this->training_id;
    }

    /**
     * Set the training_id attribute.
     *
     * @param ?int $value
     * @return void
     */
    public function setTrainingId(?int $value)
    {
        $this->training_id = $value;
    }

    /**
     * Get the date_obtained attribute.
     *
     * @return ?Carbon
     */
    public function getDateObtained(): ?Carbon
    {
        return is_null($this->date_obtained) ? null : Carbon::parse($this->date_obtained);
    }

    /**
     * Set the date_obtained attribute.
     *
     * @param ?Carbon $value
     * @return void
     */
    public function setDateObtained(?Carbon $value)
    {
        $this->date_obtained = $value;
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
