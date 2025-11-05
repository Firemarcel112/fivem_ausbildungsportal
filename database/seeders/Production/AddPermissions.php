<?php

namespace Database\Seeders\Production;

use Artisan;
use App\Models\Permission;
use App\Models\PermissionCategorie;
use Illuminate\Database\Seeder;

class AddPermissions extends Seeder
{

    public array $permissions = [
        'Ausbilder' => [
            'permissions' => [
                'is_trainer',
                'announcements',
            ],
            'rank' => 10,
        ],
        'Qualifikationen' => [
            'permissions' => [
                'user.qualifications.assign',
            ],
            'rank' => 50,
        ],
        'Benutzerverwaltung' => [
            'permissions' => [
                'usermanagement.index',
                'usermanagement.store',
                'usermanagement.edit.account_data',
                'usermanagement.edit.personal_data',
                'usermanagement.edit.permissions',
                'usermanagement.delete',
            ],
            'rank' => 40,
        ],
        'Ausbildungen' => [
            'permissions' => [
                'trainings.delete',
                'trainings.store',
                'trainings.participants.show',
            ],
            'rank' => 20,
        ],
        'Ausbildungssperren' => [
            'permissions' => [
                'trainingban.assign',
                'trainingban.show_internal_reason',
            ],
            'rank' => 30,
        ],
        'Administration' => [
            'permissions' => [
                'administration.roles.edit',
                'administration.fractions.edit',
                'administration.fractions.delete',
                'administration.qualifications.edit',
                'administration.qualifications.delete',
                'administration.requirements.delete',
            ],
            'rank' => 100,
        ],
        'Dokumente' => [
            'permissions' => [
                'documents.show.account',
                'documents.edit',
                'documents.delete',
                'documents.create',
            ],
            'rank' => 40,
        ]
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        foreach ($this->permissions as $name => $categorie) {
            $model = PermissionCategorie::firstOrNew([
                'name' => $name,
            ]);
            $model->setRank($categorie['rank'] ?? 0);
            $model->save();

            foreach ($categorie['permissions'] as $permission_name) {
                $permission = Permission::firstOrNew(
                    [
                        'name' => $permission_name
                    ]
                );
                $permission->setCategorieId($model->getId());
                $permission->save();
            }
        }

        Artisan::call('permission:cache-reset');
    }
}
