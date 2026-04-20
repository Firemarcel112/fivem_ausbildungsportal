<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $discord_account_id
 * @property int $user_id
 * @property string $discord_id
 * @property string|null $username
 * @property string|null $avatar
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 *
 * @method static Builder<static>|DiscordAccount newModelQuery()
 * @method static Builder<static>|DiscordAccount newQuery()
 * @method static Builder<static>|DiscordAccount query()
 * @method static Builder<static>|DiscordAccount whereAvatar($value)
 * @method static Builder<static>|DiscordAccount whereCreatedAt($value)
 * @method static Builder<static>|DiscordAccount whereDiscordAccountId($value)
 * @method static Builder<static>|DiscordAccount whereDiscordId($value)
 * @method static Builder<static>|DiscordAccount whereToken($value)
 * @method static Builder<static>|DiscordAccount whereUpdatedAt($value)
 * @method static Builder<static>|DiscordAccount whereUserId($value)
 * @method static Builder<static>|DiscordAccount whereUsername($value)
 *
 * @mixin \Eloquent
 */
class DiscordAccount extends BaseModel
{
    protected $table = 'discord_accounts';

    protected $primaryKey = 'discord_account_id';

    protected $guarded = [
        'discord_account_id',
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    public static function findByDiscordId(string $discord_id): ?self
    {
        return self::where('discord_id', $discord_id)
            ->first();
    }

    # ########################
    # SCOPES
    # ########################

    # ########################
    # RELATIONS
    # ########################

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################

}
