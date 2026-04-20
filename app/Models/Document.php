<?php

namespace App\Models;

use App\Models\User\Account;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Str;

/**
 * @property int $document_id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $assign_info
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DocumentLink|null $documentAssign
 * @property-read Account|null $linkedAccount
 * @property-read mixed $type
 *
 * @method static Builder<static>|Document filteredList(bool $can_edit = false, array $allowed_types = [], array $filters = [])
 * @method static Builder<static>|Document isSearch(?string $search)
 * @method static Builder<static>|Document joinDocumentAssign()
 * @method static Builder<static>|Document newModelQuery()
 * @method static Builder<static>|Document newQuery()
 * @method static Builder<static>|Document orderByAssigned(string $direction = 'asc')
 * @method static Builder<static>|Document query()
 * @method static Builder<static>|Document whereCreatedAt($value)
 * @method static Builder<static>|Document whereDescription($value)
 * @method static Builder<static>|Document whereDocumentId($value)
 * @method static Builder<static>|Document whereTitle($value)
 * @method static Builder<static>|Document whereUpdatedAt($value)
 * @method static Builder<static>|Document whereUrl($value)
 *
 * @mixin \Eloquent
 */
class Document extends BaseModel
{
    protected $table = 'documents';

    protected $primaryKey = 'document_id';

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public $guarded = ['document_id'];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Suche
     *
     * @param  Builder $query
     * @param  string  $search
     * @return Builder
     */
    public function scopeIsSearch(Builder $query, ?string $search): Builder
    {
        if (empty($search)) {
            return $query;
        }

        return $query->where(function ($query) use ($search) {
            $query->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope für Join mit DocumentAssign
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeJoinDocumentAssign(Builder $query): Builder
    {
        $document_link_table_name = (new DocumentLink)->getTable();
        $self_table_name = (new self)->getTable();

        return $query->leftJoin(
            $document_link_table_name,
            $self_table_name . '.' . self::getKeyName(),
            '=',
            $document_link_table_name . '.document_id',
        )
            ->select($self_table_name . '.*');
    }

    /**
     * Scope für Order nach Zugewiesenem Benutzer
     *
     * @param  Builder $query
     * @param  string  $direction
     * @return Builder
     */
    public function scopeOrderByAssigned(Builder $query, string $direction = 'asc'): Builder
    {
        $this->joinDocumentsLinkOnce($query);

        return $query->leftJoin('user_accounts', 'documents_link.link_id', '=', 'user_accounts.user_account_id')
            ->orderBy('user_accounts.last_name', $direction)
            ->select('documents.*');
    }

    public function joinDocumentsLinkOnce(Builder $query)
    {
        $tableName = 'documents_link';
        $joins = collect($query->getQuery()->joins);

        if (!$joins->pluck('table')->contains($tableName)) {
            $query->leftJoin($tableName, 'documents.document_id', '=', $tableName . '.document_id');
        }
    }

    /**
     * Scope für Filterung der Dokumenten Liste
     *
     * @param Builder $query
     * @param bool    $can_edit
     * @param array   $allowed_types
     * @param array   $filters
     */
    public function scopeFilteredList(Builder $query, bool $can_edit = false, array $allowed_types = [], array $filters = [])
    {
        $search = $filters['search'] ?? null;
        $sortBy = $filters['sort_by'] ?? 'created_at';

        return $query->with(['documentAssign', 'linkedAccount'])
            ->when($search, function ($q) use ($search) {
                /** @var \Illuminate\Database\Eloquent\Builder<Document> $q */
                return $q->isSearch($search);
            })
            ->joinDocumentAssign()
            ->where(function ($q) use ($can_edit, $allowed_types) {
                $tableName = (new DocumentLink)->getTable();

                $q->when($can_edit, function ($sq) use ($tableName) {
                    return $sq->orWhereNull($tableName . '.id');
                })
                    ->when(!empty($allowed_types), function ($sq) use ($allowed_types, $tableName) {
                        return $sq->orWhereIn($tableName . '.link_type', $allowed_types);
                    });
            })

            ->when(
                $sortBy == 'assigned',
                fn($q) => $q->orderByAssigned(),
                fn($q) => $q->orderBy($sortBy, 'desc')
            );
    }

    # ########################
    # RELATIONS
    # ########################

    public function documentAssign()
    {
        return $this->hasOne(
            DocumentLink::class,
            'document_id',
            'document_id',
        );
    }

    public function linkedAccount(): HasOneThrough
    {
        return $this->hasOneThrough(
            Account::class,
            DocumentLink::class,
            'document_id',
            'user_account_id',
            'document_id',
            'link_id'
        )
            ->where('link_type', 'ACCOUNT');
    }

    # ########################
    # Accessors & Mutators
    # ########################

    public function type(): Attribute
    {
        return Attribute::make(
            get: function () {
                return Str::contains($this->title, 'Zertifikat') ? 'ZERTIFIKAT' : 'DOKUMENT';
            }
        );
    }

    protected function assignInfo(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->linkedAccount) {
                    return [
                        'id' => $this->linkedAccount->getKey(),
                        'name' => $this->linkedAccount->full_name,
                        'url' => route('profile.show', $this->linkedAccount),
                    ];
                }

                return [
                    'id' => null,
                    'name' => __('general.nicht_zugeordnet'),
                    'url' => null,
                ];
            },
            set: function (?int $value) {
                if (empty($value)) {
                    return $this->documentAssign()->delete();
                }

                $this->documentAssign()->updateOrCreate(
                    [
                        'document_id' => $this->getKey(),
                    ],
                    [
                        'link_id' => $value,
                        'link_type' => 'ACCOUNT',
                    ]
                );

                return [];
            }
        );
    }
}
