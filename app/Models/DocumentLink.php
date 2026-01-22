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
     * Get the document_id attribute.
     *
     * @return int
     */
    public function getDocumentId(): int
    {
        return $this->document_id;
    }

    /**
     * Set the document_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setDocumentId(int $value)
    {
        $this->document_id = $value;
    }

    /**
     * Get the link_id attribute.
     *
     * @return int
     */
    public function getLinkId(): int
    {
        return $this->link_id;
    }

    /**
     * Set the link_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setLinkId(int $value)
    {
        $this->link_id = $value;
    }

    /**
     * Get the link_type attribute.
     *
     * @return mixed
     */
    public function getLinkType(): mixed
    {
        return $this->link_type;
    }

    /**
     * Set the link_type attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setLinkType(mixed $value)
    {
        $this->link_type = $value;
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
