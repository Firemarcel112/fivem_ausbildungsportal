<?php

namespace App\Models;

use App\Events\QualificationAction;
use App\Models\Qualifications\Requirement;
use App\Models\User\Qualification as UserQualification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

/**
 * @property int $qualification_id
 * @property string $name
 * @property int $generate_certificate Wenn 1 dann werden automatisch bei abschluss dieser Qualifikation Zertifikate erstellt
 * @property int $rank
 * @property int $hide Qualifikation wird in der Ausbidlungsliste angezeigt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read Collection<int, Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read Collection<int, Training> $trainings
 * @property-read int|null $trainings_count
 * @property-read Collection<int, UserQualification> $userQualifications
 * @property-read int|null $user_qualifications_count
 *
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
 *
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

    protected $guarded = [
        'qualification_id',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt alle sichtbaren Qualifikationen zurück, sortiert nach Standardsortierung
     *
     * @param                                                                                                                                                                          $with_hidden Wenn true, werden auch ausgeblendete Qualifikationen zurückgegeben
     * @return mixed|\Illuminate\Database\Eloquent\Collection<int, Qualification>|\Illuminate\Database\Eloquent\Collection<int, TModel>|\Illuminate\Support\Collection<int, \stdClass>
     */
    public static function getAllQualifications(bool $with_hidden = false)
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

    /**
     * @param  bool                                                                                                                                                                    $with_hidden
     * @return mixed|\Illuminate\Database\Eloquent\Collection<int, Qualification>|\Illuminate\Database\Eloquent\Collection<int, TModel>|\Illuminate\Support\Collection<int, \stdClass>
     */
    public static function getAllQualificationsWithRequirements(bool $with_hidden = false): Collection
    {
        Cache::forget('qualifications.all.with_requirements');
        if ($with_hidden) {
            return Cache::rememberForever('qualifications.all.with_hidden.with_requirements', function () {
                return self::with('requirements')
                    ->isOrderByDefault()
                    ->get();
            });
        }

        return Cache::rememberForever('qualifications.all.with_requirements', function () {
            return self::with('requirements.fraction')
                ->isVisible()
                ->isOrderByDefault()
                ->get();
        });
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Standardsortierung
     *
     * @param  Builder $query
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
     *
     * @param  Builder $query
     * @param  bool    $value
     * @return Builder
     */
    public function scopeIsVisible(Builder $query, bool $value = true)
    {
        return $query->where('hide', !$value);
    }

    # ########################
    # RELATIONS
    # #######################

    public function requirements(): HasMany
    {
        return $this->hasMany(
            Requirement::class,
            'qualification_id',
            'qualification_id'
        )
            ->orderByDefault();
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(
            Training::class,
            'qualification_id',
            'qualification_id'
        );
    }

    public function userQualifications(): HasMany
    {
        return $this->hasMany(
            UserQualification::class,
            'qualification_id',
            'qualification_id'
        );
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
