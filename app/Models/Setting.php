<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
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
 * @mixin \Eloquent
 */
class Setting extends BaseModel
{
    protected $table = 'settings';
    protected $primaryKey = 'setting_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Gibt den Value einer Einstellung zurück
     *
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    public static function value(string $key, ?string $default = null): ?string
    {
        $setting = self::isKey($key)?->first()?->value;
        return $setting ?? $default;
    }

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Key
     *
     * @param Builder $query
     * @param string $key
     * @return Builder
     */
    public function scopeIsKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    #########################
    # RELATIONS
    #########################

    #########################
    # GET & SET
    #########################

    /**
     * Get the setting_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->setting_id;
    }

    /**
     * Set the setting_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->setting_id = $value;
    }

    /**
     * Get the setting_id attribute.
     *
     * @return int
     */
    public function getSettingId(): int
    {
        return $this->setting_id;
    }

    /**
     * Set the setting_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setSettingId(int $value)
    {
        $this->setting_id = $value;
    }

    /**
     * Get the key attribute.
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the key attribute.
     *
     * @param string $value
     * @return void
     */
    public function setKey(string $value)
    {
        $this->key = $value;
    }

    /**
     * Get the value attribute.
     *
     * @return ?string
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Set the value attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setValue(?string $value)
    {
        $this->value = $value;
    }

    /**
     * Get the description attribute.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setDescription(?string $value)
    {
        $this->description = $value;
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
