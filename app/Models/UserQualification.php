<?php

namespace App\Models;

use DateTime;
use App\Models\BaseModel;

/**
 * @property int $user_qualification_id
 * @property int $user_id
 * @property int $qualification_id
 * @property int|null $training_id
 * @property string|null $date_obtained Erforderlich wenn keine Ausbildung vorliegt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereDateObtained($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereTrainingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserQualification whereUserQualificationId($value)
 * @mixin \Eloquent
 */
class UserQualification extends BaseModel
{
	protected $table = 'user_qualification';
	protected $primaryKey = 'user_qualification_id';

	protected $guarded = ['user_qualification_id'];

	#########################
	# CUSTOM FUNCTIONS
	#########################

	public function getFormattedCreatedAt()
	{
		return $this->getCreatedAt()->format('d.m.Y');
	}

	#########################
	# SCOPES
	#########################

	#########################
	# RELATIONS
	#########################

	#########################
	# GET & SET
	#########################

	public function getCreatedAt()
	{
		$date = new DateTime($this->created_at);
		return $date;
	}

	public function setUserId(int $value)
	{
		$this->user_id = $value;
	}

	public function setTrainingId(int $value)
	{
		$this->training_id = $value;
	}
}
