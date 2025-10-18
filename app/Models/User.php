<?php

namespace App\Models;

use App\Enums\Role as RoleEnum;
use App\Models\Qualifications\Qualification;
use App\Models\Trainings\TrainingBan;
use App\Models\Training\TrainingBanReason;
use App\Models\Trainings\Participant;
use App\Models\User\Account;
use App\Models\User\Qualification as UserQualification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property \App\Models\?string $name
 * @property \App\Models\?string $email
 * @property \App\Models\?Carbon $email_verified_at
 * @property string $password
 * @property \App\Models\?string $remember_token
 * @property \App\Models\?Carbon $created_at
 * @property \App\Models\?Carbon $updated_at
 * @property-read Account|null $account
 * @property-read TrainingBan|null $activeTrainingBan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DiscordAccount|null $discord
 * @property-read \App\Models\UserInfo|null $info
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualifikation> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static Builder<static>|User withIsTrainer()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, AuditingAuditable, HasRoles {
        hasPermissionTo as spatieHasPermissionTo;
        hasRole as spatieHasRole;
    }

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

    public $with = ['permissions'];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    /**
     * Sucht den Benutzer nach Benutzernamen
     *
     * @param string $username
     * @return User|null
     */
    public static function findByUsername(string $username)
    {
        return self::where('name', $username)->first();
    }

    /**
     * Gibt die Rollen des Benutzers zurück
     */
    public function getRoles()
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Gibt die Ausgaberolle des Benutzers zurück
     * @return string
     */
    public function getOutputRole()
    {
        $roles = $this->getRoles();
        foreach (RoleEnum::cases() as $role) {
            switch ($role->name) {
                case 'ADMIN':
                    $color = 'red';
                    break;
                case 'AUSBILDER':
                    $color = 'purple';
                    break;
                case 'AUSBILDER_EXTERN':
                    $color = 'pink';
                    break;
            }
            if (in_array($role->value, $roles)) {
                return "<span class='fw-bold text-{$color}'>" . __('roles.' . strtolower($role->name)) . '</span>';
            }
        }
        return "<span class='fw-bold text-green'>" . __('roles.user') . '</span>';
    }

    /**
     * Prüft die Permission
     *
     * @param mixed $permission
     * @param mixed $guardName
     * @return bool
     */
    public function hasPermissionTo($permission, $guardName = null, bool $check_superadmin = true): bool
    {
        if ($this->isSuperadmin() && $check_superadmin) {
            return true;
        }

        return $this->spatieHasPermissionTo($permission, $guardName);
    }

    /**
     * Prüft ob die Rolle vorhanden ist
     * @param mixed $roles
     * @param mixed $guard
     * @param mixed $check_superadmin
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
     * @param array $grant_permission_ids
     * @param array $revoke_permission_ids
     * @return void
     */
    public function syncPermissions(array $grant_permission_ids = [], array $revoke_permission_ids = [])
    {
        if (Auth::user()->hasPermissionTo('usermanagement.edit.permissions')) {
            foreach ($revoke_permission_ids as $permission_id) {
                $has_permission = $this->hasPermissionTo($permission_id, null, false);
                if ($has_permission) {
                    $this->auditDetach('permissions', $permission_id,  true);
                }
            }
            unset($revoke_permission_ids);


            foreach ($grant_permission_ids as $permission_id) {
                $has_permission = $this->hasPermissionTo($permission_id, null, false);
                if (!$has_permission) {
                    $this->auditAttach('permissions', $permission_id, [
                        'permission_id' => $permission_id
                    ]);
                }
            }
            unset($grant_permission_ids);
        }
    }

    /**
     * Syncronisiert die Rollen und schreibt dafür Audit einträge => typ (Attach, Detach)
     *
     * @param array $grant_role_ids
     * @param array $revoke_role_ids
     * @return void
     */
    public function syncRoles(array $grant_role_ids = [], array $revoke_role_ids = [])
    {
        if (Auth::user()->hasPermissionTo('usermanagement.edit.permissions')) {
            foreach ($revoke_role_ids as $role_id) {
                $has_role = $this->hasRole($role_id, null, false);
                if ($has_role) {
                    $this->auditDetach('roles', $role_id,  true, [
                        'role_id' => $role_id
                    ]);
                }
            }
            unset($revoke_role_ids);


            foreach ($grant_role_ids as $role_id) {
                $has_role = $this->hasRole($role_id, null, false);
                if (!$has_role) {
                    $this->auditAttach('roles', $role_id, [
                        'role_id' => $role_id
                    ]);
                }
            }
            unset($grant_role_ids);
        }
    }

    /**
     * Verteilt eine Ausbildungssperre
     * @param mixed $date_from
     * @param mixed $date_to
     * @param int $reason_id
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
        $model = new TrainingBan();
        $model->setUserId($this->getId());
        $model->setIssuerId(Auth::user()->getId());
        $model->setReason($reason);
        $model->setDateFrom(Carbon::parse($date_from));
        $model->setDateTo(Carbon::parse($date_to ?? now()->addDays(7)));
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
     * Gibt den vollen Namen zurück
     */
    public function getFullName()
    {
        return $this->account->getFullName();
    }

    /**
     * Gibt den Discord namen oder den Normalen Namen zurück
     *
     * @return string
     */
    public function getDiscordName(): string
    {
        if (!empty($this->discord)) {
            return $this?->discord?->getUsername() ?? $this->getFullName();
        }
        return $this->getFullName();
    }

    /**
     * Gibt die Aktiven Ausbilder zurück
     */
    public static function getTrainers()
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
        } elseif (in_array($this->getId(), $allowed_ids)) {
            return true;
        }
        return false;
    }

    /**
     * Gibt die Initialien des Benutzers zurück
     *
     * @return string
     */
    public function getInitials(): string
    {
        return strtoupper(substr($this->account->first_name, 0, 1)) . strtoupper(substr($this->account->last_name, 0, 1));
    }

    /**
     * Überprüft, ob der Benutzer Ausbilder ist
     *
     * @return bool
     */
    public function isTrainer(): bool
    {
        return $this->hasPermissionTo('is_trainer', null, false);
    }

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Benutzer der Ausbilder ist
     *
     * @param Builder $query
     */
    public function scopeWithIsTrainer(Builder $query): Builder
    {
        return $query
            ->with(['permissions', 'roles.permissions'])
            ->where(function (Builder $q) {
                $q->whereHas('permissions', function ($q2) {
                    $q2->where('name', 'is_trainer');
                })->orWhereHas('roles.permissions', function ($q2) {
                    $q2->where('name', 'is_trainer');
                });
            });
    }

    #########################
    # RELATIONS
    #########################

    /**
     * Gibt die RP Daten zurück
     *
     * @return HasOne<Account, User>
     */
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

    #########################
    # GET & SET
    #########################


    /**
     * Get the id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->id = $value;
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
     * Get the email attribute.
     *
     * @return ?string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the email attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setEmail(?string $value)
    {
        $this->email = $value;
    }

    /**
     * Get the email_verified_at attribute.
     *
     * @return ?Carbon
     */
    public function getEmailVerifiedAt(): ?Carbon
    {
        return is_null($this->email_verified_at) ? null : Carbon::parse($this->email_verified_at);
    }

    /**
     * Set the email_verified_at attribute.
     *
     * @param ?Carbon $value
     * @return void
     */
    public function setEmailVerifiedAt(?Carbon $value)
    {
        $this->email_verified_at = $value;
    }

    /**
     * Get the password attribute.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the password attribute.
     *
     * @param string $value
     * @return void
     */
    public function setPassword(string $value)
    {
        $this->password = $value;
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
