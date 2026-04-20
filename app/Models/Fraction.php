<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property int $fraction_id
 * @property string $name
 * @property string $short_name
 * @property string|null $discord_webhook
 * @property string|null $discord_webhook_completed
 * @property int $master Master = Fraktion ist Hauptfraktion für Abschluss
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $full_name
 *
 * @method static Builder<static>|Fraction isInFractionId(array $fraction_ids)
 * @method static Builder<static>|Fraction newModelQuery()
 * @method static Builder<static>|Fraction newQuery()
 * @method static Builder<static>|Fraction query()
 * @method static Builder<static>|Fraction whereCreatedAt($value)
 * @method static Builder<static>|Fraction whereDiscordWebhook($value)
 * @method static Builder<static>|Fraction whereDiscordWebhookCompleted($value)
 * @method static Builder<static>|Fraction whereFractionId($value)
 * @method static Builder<static>|Fraction whereMaster($value)
 * @method static Builder<static>|Fraction whereName($value)
 * @method static Builder<static>|Fraction whereShortName($value)
 * @method static Builder<static>|Fraction whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Fraction extends BaseModel
{
    protected $table = 'fractions';

    protected $primaryKey = 'fraction_id';

    protected $guarded = ['fraction_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für mehrere Fraktions-IDs
     *
     * @param  Builder $query
     * @param  array   $fraction_ids
     * @return Builder
     */
    public function scopeIsInFractionId(Builder $query, array $fraction_ids)
    {
        return $query->whereIn('fraction_id', $fraction_ids);
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->name . ' (' . $this->short_name . ')'
        );
    }
}
