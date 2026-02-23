<?php

namespace App\Models\Qualifications;

use App\Events\QualificationAction;
use App\Models\BaseModel;
use App\Models\Qualifications\Requirement;
use App\Models\Trainings\Training;
use App\Models\User\Qualification as UserQualification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $qualification_id
 * @property string $name
 * @property int $generate_certificate Wenn 1 dann werden automatisch bei abschluss dieser Qualifikation Zertifikate erstellt
 * @property int $rank
 * @property int $hide
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Training> $trainings
 * @property-read int|null $trainings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserQualification> $userQualifications
 * @property-read int|null $user_qualifications_count
 * @method static Builder<static>|Qualification isOrderByDefault()
 * @method static Builder<static>|Qualification isVisible(bool $value = true)
 * @method static Builder<static>|Qualification newModelQuery()
 * @method static Builder<static>|Qualification newQuery()
 * @method static Builder<static>|Qualification query()
 * @method static Builder<static>|Qualification whereCreatedAt($value)
 * @method static Builder<static>|Qualification whereGenerateCertificate($value)
 * @method static Builder<static>|Qualification whereHide($value)
 * @method static Builder<static>|Qualification whereName($value)
 * @method static Builder<static>|Qualification whereQualificationId($value)
 * @method static Builder<static>|Qualification whereRank($value)
 * @method static Builder<static>|Qualification whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Qualification extends BaseModel
{
    protected $table = 'qualifications';
    protected $primaryKey = 'qualification_id';

    protected $dispatchesEvents = [
        'created' => QualificationAction::class,
        'updated' => QualificationAction::class,
        'deleted' => QualificationAction::class,
    ];


    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Gibt alle sichtbaren Qualifikationen zurück, sortiert nach Standardsortierung
     *
     * @param $with_hidden Wenn true, werden auch ausgeblendete Qualifikationen zurückgegeben
     * @return mixed|\Illuminate\Database\Eloquent\Collection<int, Qualification>|\Illuminate\Database\Eloquent\Collection<int, TModel>|\Illuminate\Support\Collection<int, \stdClass>
     */
    public static function getAllQualifications($with_hidden = false)
    {
        if ($with_hidden) {
            return Cache::rememberForever('qualifications.all.with_hidden', function () {
                return self::isOrderByDefault()
                    ->get();
            });
        }
        return Cache::rememberForever('qualifications.all', function () {
            return self::isVisible()
                ->isOrderByDefault()
                ->get();
        });
    }

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
        return $query->orderBy('hide')
            ->orderBy('rank')
            ->orderBy('name');
    }

    /**
     * Scope für sichtbare oder ausgeblendete Qualifikationen
     * @param Builder $query
     * @param bool $value
     * @return Builder
     */
    public function scopeIsVisible(Builder $query, bool $value = true)
    {
        return $query->where('hide', !$value);
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

    public function trainings()
    {
        return $this->hasMany(
            Training::class,
            'qualification_id',
            'qualification_id'
        );
    }

    public function userQualifications()
    {
        return $this->hasMany(
            UserQualification::class,
            'qualification_id',
            'qualification_id'
        );
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

    /**
     * Get the generate_certificate attribute.
     *
     * @return int
     */
    public function getGenerateCertificate(): int
    {
        return $this->generate_certificate;
    }

    /**
     * Set the generate_certificate attribute.
     *
     * @param int $value
     * @return void
     */
    public function setGenerateCertificate(int $value)
    {
        $this->generate_certificate = $value;
    }

    /**
     * Get the hide attribute.
     *
     * @return int
     */
    public function getHide(): int
    {
        return $this->hide;
    }

    /**
     * Set the hide attribute.
     *
     * @param int $value
     * @return void
     */
    public function setHide(int $value)
    {
        $this->hide = $value;
    }
}
