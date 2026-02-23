<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $id
 * @property int $document_id
 * @property int $link_id
 * @property string $link_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static Builder<static>|DocumentLink newModelQuery()
 * @method static Builder<static>|DocumentLink newQuery()
 * @method static Builder<static>|DocumentLink query()
 * @method static Builder<static>|DocumentLink whereCreatedAt($value)
 * @method static Builder<static>|DocumentLink whereDocumentId($value)
 * @method static Builder<static>|DocumentLink whereId($value)
 * @method static Builder<static>|DocumentLink whereLinkId($value)
 * @method static Builder<static>|DocumentLink whereLinkType($value)
 * @method static Builder<static>|DocumentLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DocumentLink extends BaseModel
{
    protected $table = 'documents_link';
    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    #########################
    # RELATIONS
    #########################

    #########################
    # ACCESSORS & MUTATORS
    #########################
}
