<?php

namespace Database\Seeders\Production;

use App\Models\Fractions\Fraction;
use Artisan;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class AddPermissions extends Seeder
{

    public array $permissions = [
        'is_trainer',
        'user.qualifications.assign',
        'usermanagement.index',
        'usermanagement.store',
        'usermanagement.edit.account_data',
        'usermanagement.edit.personal_data',
        'usermanagement.edit.permissions',
        'trainings.delete',
        'trainingban.assign',
        'trainingban.show_internal_reason',
        'trainings.store',
        'trainings.participants.show',
        'announcements',
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->permissions as $permission) {
            $model = Permission::firstOrNew(['name' => $permission]);

            $model->save();
        }
        Artisan::call('permission:cache-reset');
    }
}
