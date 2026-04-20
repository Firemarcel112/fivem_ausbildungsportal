<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditingAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Stevebauman\Purify\Facades\Purify;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 *
 * @mixin \Eloquent
 */
class BaseModel extends Model implements Auditable
{
    use AuditingAuditable;

    /**
     * Purify HTML
     *
     * @param  array|string $string
     * @return array|string
     */
    public function clean(array|string $string)
    {
        return Purify::clean($string);
    }
}
