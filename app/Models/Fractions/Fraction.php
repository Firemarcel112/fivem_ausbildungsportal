<?php

namespace App\Models\Fractions;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
 * @mixin \Eloquent
 */
class Fraction extends BaseModel
{
    protected $table = 'fractions';
    protected $primaryKey = 'fraction_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Gibt den Vollen Namen & Bezeichnung zurück
     */
    public function getFullName()
    {
        return $this->name . ' (' . $this->short_name . ')';
    }

    #########################
    # SCOPES
    #########################


    #########################
    # GET & SET
    #########################

    /**
     * Get the fraction_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->attributes['fraction_id'];
    }

    /**
     * Set the fraction_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->fraction_id = $value;
    }

    /**
     * Get the fraction_id attribute.
     *
     * @return int
     */
    public function getFractionId(): int
    {
        return $this->attributes['fraction_id'];
    }

    /**
     * Set the fraction_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setFractionId(int $value)
    {
        $this->fraction_id = $value;
    }

    /**
     * Get the name attribute.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
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
     * Get the short_name attribute.
     *
     * @return string
     */
    public function getShortName(): string
    {
        return $this->attributes['short_name'];
    }

    /**
     * Set the short_name attribute.
     *
     * @param string $value
     * @return void
     */
    public function setShortName(string $value)
    {
        $this->short_name = $value;
    }

    /**
     * Get the discord_webhook attribute.
     *
     * @return ?string
     */
    public function getDiscordWebhook(): ?string
    {
        return $this->attributes['discord_webhook'];
    }

    /**
     * Set the discord_webhook attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setDiscordWebhook(?string $value)
    {
        $this->discord_webhook = $value;
    }

    /**
     * Get the discord_webhook_completed attribute.
     *
     * @return ?string
     */
    public function getDiscordWebhookCompleted(): ?string
    {
        return $this->attributes['discord_webhook_completed'];
    }

    /**
     * Set the discord_webhook_completed attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setDiscordWebhookCompleted(?string $value)
    {
        $this->discord_webhook_completed = $value;
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
     * Set the master attribute.
     *
     * @param int $value
     * @return void
     */
    public function setMaster(int $value)
    {
        $this->master = $value;
    }

    /**
     * Get the master attribute.
     *
     * @return int
     */
    public function getMaster(): int
    {
        return $this->attributes['master'];
    }
}
