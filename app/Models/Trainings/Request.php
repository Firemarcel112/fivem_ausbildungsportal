<?php

namespace App\Models\Trainings;

use App\Models\BaseModel;
use App\Models\Qualifications\Qualification;
use App\Models\Qualifikation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $training_request_id
 * @property int|null $user_id
 * @property int $qualification_id
 * @property string $date
 * @property string $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Qualification|null $qualification
 * @property-read User|null $user
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
 * @mixin \Eloquent
 */
class Request extends BaseModel
{
    protected $table = 'training_requests';
    protected $primaryKey = 'training_request_id';

    protected $guarded = [
        'training_request_id',
    ];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    #########################
    # RELATIONS
    #########################

    public function user()
    {
        return $this->hasOne(
            User::class,
            'id',
            'user_id'
        );
    }

    public function qualification()
    {
        return $this->hasOne(
            Qualification::class,
            'qualification_id',
            'qualification_id'
        );
    }

    #########################
    # GET & SET
    #########################
    /**
     * Get the training_request_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->training_request_id;
    }

    /**
     * Set the training_request_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->training_request_id = $value;
    }

    /**
     * Get the training_request_id attribute.
     *
     * @return int
     */
    public function getTrainingRequestId(): int
    {
        return $this->training_request_id;
    }

    /**
     * Set the training_request_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setTrainingRequestId(int $value)
    {
        $this->training_request_id = $value;
    }

    /**
     * Get the user_id attribute.
     *
     * @return ?int
     */
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    /**
     * Set the user_id attribute.
     *
     * @param ?int $value
     * @return void
     */
    public function setUserId(?int $value)
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
     * Get the date attribute.
     *
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return Carbon::parse($this->date);
    }

    /**
     * Set the date attribute.
     *
     * @param Carbon $value
     * @return void
     */
    public function setDate(Carbon $value)
    {
        $this->date = $value;
    }

    /**
     * Get the time attribute.
     *
     * @return Carbon
     */
    public function getTime(): Carbon
    {
        return Carbon::parse($this->time);
    }

    /**
     * Set the time attribute.
     *
     * @param Carbon $value
     * @return void
     */
    public function setTime(Carbon $value)
    {
        $this->time = $value;
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
