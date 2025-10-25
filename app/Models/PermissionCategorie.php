<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $permission_categorie_id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @method static Builder<static>|PermissionCategorie newModelQuery()
 * @method static Builder<static>|PermissionCategorie newQuery()
 * @method static Builder<static>|PermissionCategorie orderByDefault()
 * @method static Builder<static>|PermissionCategorie query()
 * @method static Builder<static>|PermissionCategorie whereCreatedAt($value)
 * @method static Builder<static>|PermissionCategorie whereName($value)
 * @method static Builder<static>|PermissionCategorie wherePermissionCategorieId($value)
 * @method static Builder<static>|PermissionCategorie whereRank($value)
 * @method static Builder<static>|PermissionCategorie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PermissionCategorie extends BaseModel
{
    protected $table = 'permission_categories';
    protected $primaryKey = 'permission_categorie_id';

    protected $guarded = ['permission_categorie_id'];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    /**
     * Scope für Standardsortierung
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query)
    {
        return $query->orderBy('rank', 'asc');
    }

    #########################
    # RELATIONS
    #########################

    public function permissions()
    {
        return $this->hasMany(
            Permission::class,
            'categorie_id',
            'permission_categorie_id'
        );
    }

    #########################
    # GET & SET
    #########################

    /**
     * Get the permission_categorie_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->permission_categorie_id;
    }

    /**
     * Set the permission_categorie_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->permission_categorie_id = $value;
    }

    /**
     * Get the permission_categorie_id attribute.
     *
     * @return int
     */
    public function getPermissionCategorieId(): int
    {
        return $this->permission_categorie_id;
    }

    /**
     * Set the permission_categorie_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setPermissionCategorieId(int $value)
    {
        $this->permission_categorie_id = $value;
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
}
