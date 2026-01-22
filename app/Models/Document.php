<?php

namespace App\Models;

use App\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int $document_id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DocumentLink|null $documentAssign
 * @method static Builder<static>|Document newModelQuery()
 * @method static Builder<static>|Document newQuery()
 * @method static Builder<static>|Document query()
 * @method static Builder<static>|Document whereCreatedAt($value)
 * @method static Builder<static>|Document whereDescription($value)
 * @method static Builder<static>|Document whereDocumentId($value)
 * @method static Builder<static>|Document whereTitle($value)
 * @method static Builder<static>|Document whereUpdatedAt($value)
 * @method static Builder<static>|Document whereUrl($value)
 * @mixin \Eloquent
 */
class Document extends BaseModel
{
    protected $table = 'documents';
    protected $primaryKey = 'document_id';

    #########################
    # CUSTOM FUNCTIONS
    #########################

    #########################
    # SCOPES
    #########################

    #########################
    # RELATIONS
    #########################

    public function documentAssign()
    {
        return $this->hasOne(
            DocumentLink::class,
            'document_id',
            'document_id',
        );
    }

    #########################
    # GET & SET
    #########################

    /**
     * Get the document_id attribute.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->document_id;
    }

    /**
     * Set the document_id attribute.
     *
     * @param int $value
     * @return void
     */
    public function setId(int $value)
    {
        $this->document_id = $value;
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
     * Get the title attribute.
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set the title attribute.
     *
     * @param string $value
     * @return void
     */
    public function setTitle(string $value)
    {
        $this->title = $value;
    }

    /**
     * Get the description attribute.
     *
     * @return ?string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Set the description attribute.
     *
     * @param ?string $value
     * @return void
     */
    public function setDescription(?string $value)
    {
        $this->description = $value;
    }

    /**
     * Get the url attribute.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set the url attribute.
     *
     * @param string $value
     * @return void
     */
    public function setUrl(string $value)
    {
        $this->url = $value;
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
