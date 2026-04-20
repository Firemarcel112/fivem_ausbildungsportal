<?php

namespace App\Models;

use App\Observers\SettingsObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $setting_id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 *
 * @method static Builder<static>|Setting isKey(string $key)
 * @method static Builder<static>|Setting newModelQuery()
 * @method static Builder<static>|Setting newQuery()
 * @method static Builder<static>|Setting query()
 * @method static Builder<static>|Setting whereCreatedAt($value)
 * @method static Builder<static>|Setting whereDescription($value)
 * @method static Builder<static>|Setting whereKey($value)
 * @method static Builder<static>|Setting whereSettingId($value)
 * @method static Builder<static>|Setting whereUpdatedAt($value)
 * @method static Builder<static>|Setting whereValue($value)
 *
 * @mixin \Eloquent
 */
#[ObservedBy(SettingsObserver::class)]
class Setting extends BaseModel
{
    protected $table = 'settings';

    protected $primaryKey = 'setting_id';

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt den Value einer Einstellung zurück
     *
     * @param  string      $key
     * @param  string|null $default
     * @return string|null
     */
    public static function getValue(string $key, ?string $default = null): ?string
    {
        $setting = self::isKey($key)->first()?->value;

        return $setting ?? $default;
    }

    /**
     * Legt die Auditing Daten fest
     *
     * @param  array $data
     * @return array
     */
    public function transformAudit(array $data): array
    {
        $data['auditable_id'] = $this->getKey();

        return $data;
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Key
     *
     * @param  Builder $query
     * @param  string  $key
     * @return Builder
     */
    public function scopeIsKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    # ########################
    # RELATIONS
    # ########################

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
