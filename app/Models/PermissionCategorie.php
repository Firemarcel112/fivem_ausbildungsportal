<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
 *
 * @method static Builder<static>|PermissionCategorie newModelQuery()
 * @method static Builder<static>|PermissionCategorie newQuery()
 * @method static Builder<static>|PermissionCategorie orderByDefault()
 * @method static Builder<static>|PermissionCategorie query()
 * @method static Builder<static>|PermissionCategorie whereCreatedAt($value)
 * @method static Builder<static>|PermissionCategorie whereName($value)
 * @method static Builder<static>|PermissionCategorie wherePermissionCategorieId($value)
 * @method static Builder<static>|PermissionCategorie whereRank($value)
 * @method static Builder<static>|PermissionCategorie whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class PermissionCategorie extends BaseModel
{
    protected $table = 'permission_categories';

    protected $primaryKey = 'permission_categorie_id';

    protected $guarded = ['permission_categorie_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Standardsortierung
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return Builder
     */
    public function scopeOrderByDefault(Builder $query)
    {
        return $query->orderBy('rank', 'asc');
    }

    # ########################
    # RELATIONS
    # ########################

    public function permissions(): HasMany
    {
        return $this->hasMany(
            Permission::class,
            'categorie_id',
            'permission_categorie_id'
        );
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################
}
