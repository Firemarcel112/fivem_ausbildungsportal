<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BaseModel query()
 */
	class BaseModel extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models{
/**
 * @property int $discord_account_id
 * @property int $user_id
 * @property string $discord_id
 * @property string|null $username
 * @property string|null $avatar
 * @property string|null $token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereDiscordAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereDiscordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DiscordAccount whereUsername($value)
 */
	class DiscordAccount extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $document_id
 * @property string $title
 * @property string|null $description
 * @property string $url
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $assign_info
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DocumentLink|null $documentAssign
 * @property-read \App\Models\User\Account|null $linkedAccount
 * @property-read mixed $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document filteredList(bool $can_edit = false, array $allowed_types = [], array $filters = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document isSearch(?string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document joinDocumentAssign()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document orderByAssigned(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Document whereUrl($value)
 */
	class Document extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $document_id
 * @property int $link_id
 * @property string $link_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereDocumentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereLinkType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DocumentLink whereUpdatedAt($value)
 */
	class DocumentLink extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $fraction_id
 * @property string $name
 * @property string $short_name
 * @property string|null $discord_webhook
 * @property string|null $discord_webhook_completed
 * @property int $master Master = Fraktion ist Hauptfraktion für Abschluss
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $full_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction isInFractionId(array $fraction_ids)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereDiscordWebhook($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereDiscordWebhookCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereFractionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereMaster($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereShortName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereUpdatedAt($value)
 */
	class Fraction extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property int|null $categorie_id
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read mixed $translated_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCategorieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie orderByDefault()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie wherePermissionCategorieId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PermissionCategorie whereUpdatedAt($value)
 */
	class PermissionCategorie extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $qualification_id
 * @property string $name
 * @property int $generate_certificate Wenn 1 dann werden automatisch bei abschluss dieser Qualifikation Zertifikate erstellt
 * @property int $rank
 * @property int $hide
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualifications\Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Training> $trainings
 * @property-read int|null $trainings_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Qualification> $userQualifications
 * @property-read int|null $user_qualifications_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification isOrderByDefault()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification isVisible(bool $value = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereGenerateCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereUpdatedAt($value)
 */
	class Qualification extends \Eloquent {}
}

namespace App\Models\Qualifications{
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
 * @property-read \App\Models\Fraction|null $fraction
 * @property-read \App\Models\Qualification|null $qualification
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement joinOrderedQualifications()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement orderByDefault()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereFractionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereRequirementId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Requirement whereUpdatedAt($value)
 */
	class Requirement extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $setting_id
 * @property string $key
 * @property string|null $value
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting isKey(string $key)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereSettingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Setting whereValue($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $training_id
 * @property int $trainer_id
 * @property int $qualification_id
 * @property int|null $fraktion_id
 * @property string|null $meeting_point
 * @property string|null $additional_information
 * @property \Illuminate\Support\Carbon $date
 * @property \Illuminate\Support\Carbon $time
 * @property int $max_participants
 * @property int $min_participants
 * @property int $completed
 * @property int $canceled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $count_participants
 * @property-read mixed $name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trainings\Participant> $participants
 * @property-read int|null $participants_count
 * @property-read \App\Models\Qualification|null $qualification
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualifications\Requirement> $requirements
 * @property-read int|null $requirements_count
 * @property-read \App\Models\User|null $trainer
 * @property-read mixed $trainer_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training isAvailable()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training isCompletedBuilder(int $available = 1)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training orderByDefault(string $direction = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereAdditionalInformation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereCanceled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereFraktionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereMaxParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereMeetingPoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereMinParticipants($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereTrainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereTrainingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Training whereUpdatedAt($value)
 */
	class Training extends \Eloquent {}
}

namespace App\Models\Trainings{
/**
 * @property int $training_participant_id
 * @property int $training_id
 * @property int $user_id
 * @property int $present
 * @property int $logged_out
 * @property int $passed
 * @property string|null $notices
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\Account|null $account
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read mixed $full_name
 * @property-read \App\Models\Training|null $training
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant isTrainingId(int $training_id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant isUserId(int $user_id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereLoggedOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereNotices($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant wherePassed($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant wherePresent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereTrainingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereTrainingParticipantId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Participant whereUserId($value)
 */
	class Participant extends \Eloquent {}
}

namespace App\Models\Trainings{
/**
 * @property int $training_request_id
 * @property int|null $user_id
 * @property int $qualification_id
 * @property string $date
 * @property string $time
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\Qualification|null $qualification
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereTrainingRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Request whereUserId($value)
 */
	class Request extends \Eloquent {}
}

namespace App\Models\Trainings{
/**
 * @property int $training_ban_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon $date_from
 * @property \Illuminate\Support\Carbon $date_to
 * @property string $reason
 * @property int $issuer_id
 * @property string|null $internal_note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\User|null $issuer
 * @property-read mixed $issuer_name
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan isValid()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereDateFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereDateTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereInternalNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereIssuerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereTrainingBanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TrainingBan whereUserId($value)
 */
	class TrainingBan extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User\Account|null $account
 * @property-read \App\Models\Trainings\TrainingBan|null $activeTrainingBan
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \App\Models\DiscordAccount|null $discord
 * @property-read mixed $full_name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User searchAccount(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withIsTrainer()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \OwenIt\Auditing\Contracts\Auditable {}
}

namespace App\Models\User{
/**
 * @property int $user_account_id
 * @property int $user_id
 * @property string $first_name
 * @property string $last_name
 * @property \Illuminate\Support\Carbon $date_of_birth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $gender Bestimmt die Anrede auf dem Zertifikat M = Herr / W = Frau / D = Ohne Anrede
 * @property string|null $birth_location Bestimmt den Geburtsort auf dem Zertifikat
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Document> $certificates
 * @property-read int|null $certificates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User\Qualification> $directQualifications
 * @property-read int|null $direct_qualifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Fraction> $fractions
 * @property-read int|null $fractions_count
 * @property-read mixed $full_name
 * @property-read mixed $initials
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Qualification> $qualifications
 * @property-read int|null $qualifications_count
 * @property-read mixed $salutation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Trainings\Participant> $trainings
 * @property-read int|null $trainings_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account isSearch(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereBirthLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereDateOfBirth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereGender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUserAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUserId($value)
 */
	class Account extends \Eloquent {}
}

namespace App\Models\User{
/**
 * @property int $user_fraction_id
 * @property int $user_id
 * @property int $fraction_id
 * @property int $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \OwenIt\Auditing\Models\Audit> $audits
 * @property-read int|null $audits_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereFractionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereUserFractionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Fraction whereUserId($value)
 */
	class Fraction extends \Eloquent {}
}

namespace App\Models\User{
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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification isQualificationId(int $qualification_id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereDateObtained($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereQualificationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereTrainingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Qualification whereUserQualificationId($value)
 */
	class Qualification extends \Eloquent {}
}

