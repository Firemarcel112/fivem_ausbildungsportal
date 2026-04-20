<?php

namespace App\Models;

use App\Models\Trainings\Participant;
use App\Models\Trainings\TrainingBan;
use App\Models\User\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Account|null $account
 * @property-read TrainingBan|null $activeTrainingBan
 * @property-read Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DiscordAccount|null $discord
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 *
 * @method static Builder<static>|User isUserId(int $user_id)
 * @method static Builder<static>|User joinAccounts()
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static Builder<static>|User searchAccount(string $search)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User withIsTrainer()
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, $guard = null)
 *
 * @mixin \Eloquent
 */
class User extends Authenticatable implements Auditable
{
    use AuditingAuditable;

    // use HasFactory;
    use HasRoles {
        hasPermissionTo as spatieHasPermissionTo;
        hasRole as spatieHasRole;
    }
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Sucht den Benutzer nach Benutzernamen
     *
     * @param  string    $username
     * @return User|null
     */
    public static function findByUsername(string $username)
    {
        return self::firstWhere('name', $username);
    }

    /**
     * Prüft ob die Rolle vorhanden ist
     *
     * @param  mixed   $roles
     * @param  ?string $guard
     * @param  bool    $check_superadmin
     * @return bool
     */
    public function hasRole($roles, ?string $guard = null, bool $check_superadmin = true): bool
    {
        if ($this->isSuperadmin() && $check_superadmin) {
            return true;
        }

        return $this->spatieHasRole($roles, $guard);
    }

    /**
     * Syncronisiert die Permissions und schreibt dafür Audit einträge => typ (Attach, Detach)
     *
     * @param  array $grant_permission_ids
     * @param  array $revoke_permission_ids
     * @return void
     */
    public function syncPermissions(array $grant_permission_ids = [], array $revoke_permission_ids = [])
    {
        if (Auth::user()->hasPermissionTo('usermanagement.edit.permissions')) {
            foreach ($revoke_permission_ids as $permission_id) {
                $has_permission = $this->hasPermissionTo($permission_id, null);
                if ($has_permission) {
                    $this->auditDetach('permissions', $permission_id, true);
                }
            }
            unset($revoke_permission_ids);

            foreach ($grant_permission_ids as $permission_id) {
                $has_permission = $this->hasPermissionTo($permission_id, null);
                if (!$has_permission) {
                    $this->auditAttach('permissions', $permission_id, [
                        'permission_id' => $permission_id,
                    ]);
                }
            }
            unset($grant_permission_ids);
        }
    }

    /**
     * Syncronisiert die Rollen und schreibt dafür Audit einträge => typ (Attach, Detach)
     *
     * @param  array $grant_role_ids
     * @param  array $revoke_role_ids
     * @return void
     */
    public function syncRoles(array $grant_role_ids = [], array $revoke_role_ids = [])
    {
        if (Auth::user()->hasPermissionTo('usermanagement.edit.permissions')) {
            foreach ($revoke_role_ids as $role_id) {
                $has_role = $this->hasRole($role_id, null, false);
                if ($has_role) {
                    $this->auditDetach('roles', $role_id, true);
                }
            }
            unset($revoke_role_ids);

            foreach ($grant_role_ids as $role_id) {
                $has_role = $this->hasRole($role_id, null, false);
                if (!$has_role) {
                    $this->auditAttach('roles', $role_id, [
                        'role_id' => $role_id,
                    ]);
                }
            }
            unset($grant_role_ids);
        }
    }

    /**
     * Verteilt eine Ausbildungssperre
     *
     * @param  string $reason
     * @param  mixed  $date_from
     * @param  mixed  $date_to
     * @param  string $notice
     * @return bool
     */
    public function banForTrainings(
        string $reason,
        mixed $date_from = null,
        mixed $date_to = null,
        string $notice = ''
    ) {
        if (empty($reason)) {
            return false;
        }
        $date_from = $date_from ?? date('Y-m-d');
        $date_to = $date_to ?? now()->addDays(7);
        $model = new TrainingBan;
        $model->setUserId($this->getId());
        $model->setIssuerId(Auth::user()->getId());
        $model->setReason($reason);
        $model->setDateFrom(Carbon::parse($date_from));
        $model->setDateTo(Carbon::parse($date_to));
        $model->setInternalNote($notice);

        $trainings = Participant::with(['training' => function ($query) use ($date_from, $date_to) {
            $query->whereBetween('date', [$date_from, $date_to]);
        }])
            ->where('user_id', $this->getId())
            ->whereHas('training', function ($query) use ($date_from, $date_to) {
                $query->whereBetween('date', [$date_from, $date_to]);
            })
            ->get();
        foreach ($trainings as $training) {
            $training->delete();
        }

        return $model->save();
    }

    /**
     * Gibt die Aktiven Ausbilder zurück
     *
     * @return Collection<User>
     */
    public static function getTrainers(): Collection
    {
        return User::with('account')
            ->withIsTrainer()
            ->get();
    }

    public function isSuperadmin()
    {
        $allowed_ids = explode(',', config('app.superadmins'));
        if (config('app.superadmins') == '*' && config('app.env') != 'production') {
            return true;
        } elseif (in_array($this->getKey(), $allowed_ids)) {
            return true;
        }

        return false;
    }

    /**
     * Überprüft, ob der Benutzer Ausbilder ist
     *
     * @return bool
     */
    public function isTrainer(): bool
    {
        return $this->can('is_trainer', [null, 'ignore-superadmin']);
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Benutzer der Ausbilder ist
     *
     * @param Builder $query
     */
    public function scopeWithIsTrainer(Builder $query): Builder
    {
        return $query->permission('is_trainer');
    }

    /**
     * Scope für User ID
     *
     * @param  Builder<User> $query
     * @param  int           $user_id
     * @return Builder<User>
     */
    public function scopeIsUserId(Builder $query, int $user_id): Builder
    {
        return $query->where($this->getKeyName(), $user_id);
    }

    /**
     * @param  Builder<User> $query
     * @param  string        $search
     * @return Builder<User>
     */
    public function scopeSearchAccount(Builder $query, string $search)
    {
        return $query->whereHas('account', function (Builder $q) use ($search) {
            /** @phpstan-ignore-next-line */
            /** @var Builder & Account $q */
            return $q->isSearch($search);
        });
    }

    public function scopeJoinAccounts(Builder $query): Builder
    {
        $self_table_name = (new self)->getTable();

        $this->joinAccountsOnce($query);

        return $query->select($self_table_name . '.*');
    }

    public function joinAccountsOnce(Builder $query)
    {
        $table_name = (new Account)->getTable();
        $self_table_name = (new self)->getTable();

        $joins = collect($query->getQuery()->joins);

        if (!$joins->pluck('table')->contains($table_name)) {
            $query->leftJoin(
                $table_name,
                $self_table_name . '.id',
                '=',
                $table_name . '.user_id'
            );
        }
    }

    # ########################
    # RELATIONS
    # ########################

    public function account(): HasOne
    {
        return $this->hasOne(Account::class, 'user_id', 'id');
    }

    public function discord(): HasOne
    {
        return $this->hasOne(DiscordAccount::class, 'user_id', 'id');
    }

    public function activeTrainingBan(): HasOne
    {
        return $this->hasOne(
            TrainingBan::class,
            'user_id',
            'id'
        )->where('date_from', '<=', date('Y-m-d'))
            ->where('date_to', '>=', date('Y-m-d'))
            ->orderBy('date_to', 'DESC');
    }

    # ########################
    # ACCESSORS & MUTATORS
    # #########################

    public function fullName(): Attribute
    {
        return Attribute::make(
            function () {
                return $this->account?->full_name ?? __('general.unbekannt');
            },
        );
    }
}
