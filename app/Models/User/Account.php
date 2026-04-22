<?php

namespace App\Models\User;

use App\Enums\Gender;
use App\Models\BaseModel;
use App\Models\Document;
use App\Models\Fraction;
use App\Models\Qualification;
use App\Models\Trainings\Participant;
use App\Models\User;
use App\Models\User\Fraction as UserFraction;
use App\Models\User\Qualification as UserQualification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $user_account_id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property Gender $gender Bestimmt die Anrede auf dem Zertifikat M = Herr / W = Frau / D = Ohne Anrede
 * @property Carbon $date_of_birth
 * @property string|null $birth_location Bestimmt den Geburtsort auf dem Zertifikat
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $certificates
 * @property-read int|null $certificates_count
 * @property-read mixed $default_fraction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, UserQualification> $directQualifications
 * @property-read int|null $direct_qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Fraction> $fractions
 * @property-read int|null $fractions_count
 * @property-read mixed $full_name
 * @property-read mixed $initials
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read mixed $salutation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Participant> $trainings
 * @property-read int|null $trainings_count
 * @property-read User|null $user
 *
 * @method static Builder<static>|Account isNotInIds(array $account_ids)
 * @method static Builder<static>|Account isSearch(string $search)
 * @method static Builder<static>|Account newModelQuery()
 * @method static Builder<static>|Account newQuery()
 * @method static Builder<static>|Account query()
 * @method static Builder<static>|Account whereBirthLocation($value)
 * @method static Builder<static>|Account whereCreatedAt($value)
 * @method static Builder<static>|Account whereDateOfBirth($value)
 * @method static Builder<static>|Account whereFirstName($value)
 * @method static Builder<static>|Account whereGender($value)
 * @method static Builder<static>|Account whereLastName($value)
 * @method static Builder<static>|Account whereUpdatedAt($value)
 * @method static Builder<static>|Account whereUserAccountId($value)
 * @method static Builder<static>|Account whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Account extends BaseModel
{
    protected $table = 'user_accounts';

    protected $primaryKey = 'user_account_id';

    protected $guarded = [
        'user_account_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'gender' => Gender::class,
    ];

    # ########################
    # CUSTOM FUNCTIONS
    # ########################

    /**
     * Gibt die Standard Fraktion zurück
     *
     * @return int
     */
    public function getDefaultFractionId(): int
    {
        return $this->fractions->firstWhere('pivot.default', 1)->getKey();
    }

    public function getDefaultFraction(): mixed
    {
        return $this->fractions->firstWhere('pivot.default', 1);
    }

    # ########################
    # SCOPES
    # ########################

    /**
     * Scope für Suche
     *
     * @param  Builder<Account> $query
     * @param  string           $search
     * @return Builder<Account>
     */
    public function scopeIsSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($query) use ($search) {
            $query->where('first_name', 'LIKE', "{$search}%")
                ->orWhere('last_name', 'LIKE', "{$search}%")
                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["{$search}%"]);
        });
    }

    /**
     * Scope für Nicht in Account Ids
     *
     * @param  Builder<Account> $query
     * @param  array<int>       $account_ids
     * @return Builder<Account>
     */
    public function scopeIsNotInIds(Builder $query, array $account_ids)
    {
        return $query->whereNotIn($this->getKeyName(), $account_ids);
    }

    # ########################
    # RELATIONS
    # ########################

    /**
     * @return BelongsTo<\App\Models\User, \App\Models\User\Account>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return HasMany<\App\Models\Trainings\Participant, \App\Models\User\Account>
     */
    public function trainings(): HasMany
    {
        return $this->hasMany(
            Participant::class,
            'user_id',
            'user_id'
        );
    }

    /**
     * @return BelongsToMany<Fraction, \App\Models\User\Account, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function fractions(): BelongsToMany
    {
        return $this->belongsToMany(
            Fraction::class,
            (new UserFraction)->getTable(),
            'user_id',
            'fraction_id'
        )->withPivot('created_at', 'default');
    }

    /**
     * @return BelongsToMany<Qualification, \App\Models\User\Account, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function qualifications(): BelongsToMany
    {
        return $this->belongsToMany(
            Qualification::class,
            (new UserQualification)->getTable(),
            'user_id',
            'qualification_id'
        )->withPivot('created_at', 'training_id');
    }

    /**
     * @return HasMany<User\Qualification, \App\Models\User\Account>
     */
    public function directQualifications(): HasMany
    {
        return $this->hasMany(
            UserQualification::class,
            'user_id',
            'user_id'
        );
    }

    /**
     * @return BelongsToMany<Document, \App\Models\User\Account, \Illuminate\Database\Eloquent\Relations\Pivot>
     */
    public function certificates(): BelongsToMany
    {
        return $this->belongsToMany(
            Document::class,
            'documents_link',
            'link_id',
            'document_id',
        )
            ->where('link_type', 'ACCOUNT');
    }

    # ########################
    # ACCESSORS & MUTATORS
    # ########################

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->first_name . ' ' . $this->last_name;
            }
        );
    }

    public function salutation(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->gender->salutation();
            }
        );
    }

    public function initials(): Attribute
    {
        return Attribute::make(
            function () {
                return str($this->first_name)->substr(0, 1)
                    ->append(str($this->last_name)->substr(0, 1))
                    ->upper()
                    ->value();
            }
        );
    }

    public function defaultFraction(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->fractions->firstWhere('pivot.default', 1),
        );
    }
}
