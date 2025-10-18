<?php

namespace App\Models;

use App\Models\BaseModel;
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
 * @mixin \Eloquent
 */
class DiscordAccount extends BaseModel
{
	protected $table = 'discord_accounts';
	protected $primaryKey = 'discord_account_id';

	protected $guarded = [
		'discord_account_id',
	];

	#########################
	# CUSTOM FUNCTIONS
	#########################

	public static function findByDiscordId(string $discord_id): ?self
	{
		return self::where('discord_id', $discord_id)
			->first();
	}

	#########################
	# SCOPES
	#########################


	#########################
	# RELATIONS
	#########################

	public function user()
	{
		return $this->hasOne(User::class, 'id', 'user_id');
	}

	#########################
	# GET & SET
	#########################

	public function getId()
	{
		return $this->discord_account_id;
	}

	public function getUserId()
	{
		return $this->user_id;
	}

	public function setUserId(int $value)
	{
		$this->user_id = $value;
	}

	public function getDiscordId()
	{
		return $this->discord_id;
	}

	public function setDiscordId(string $value)
	{
		$this->discord_id = $value;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function setUsername(string $value)
	{
		$this->username = $this->clean($value);
	}

	public function getAvatar()
	{
		return $this->avatar;
	}

	public function setAvatar(string $value)
	{
		$this->avatar = $value;
	}

	public function getToken()
	{
		return $this->token;
	}

	public function setToken(string $value)
	{
		$this->token = $value;
	}
}
