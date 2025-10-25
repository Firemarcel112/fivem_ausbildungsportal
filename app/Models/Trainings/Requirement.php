<?php

namespace App\Models\Trainings;

use App\Models\Qualifications\Requirement as QualificationRequirement;

/**
 * @property int $requirement_id
 * @property int $qualification_id
 * @property int $fraction_id
 * @property string $name
 * @property int $rank
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement newQuery()
 * @method static Builder<static>|Requirement orderByDefault()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereFractionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereRequirementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Requirement extends QualificationRequirement {}
