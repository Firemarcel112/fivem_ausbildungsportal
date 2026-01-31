<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddDefaultSettings extends Seeder
{

    public array $settings = [
        [
            'key' => 'default_meeting_point',
            'value' => null,
            'description' => 'Standard Treffpunkt für Ausbildungen'
        ],
        [
            'key' => 'enroll_deadline',
            'value' => 10,
            'description' => 'Anmeldeschluss in Minuten vor Ausbildungsbeginn',
        ],
        [
            'key' => 'training_complete_minutes',
            'value' => 10,
            'description' => 'Zeit in Minuten bevor die Ausbildung beginnt ob diese schon abgeschlossen werden darf',
        ],
        [
            'key' => 'certificate_organization_name',
            'value' => 'Organisation',
            'description' => 'Name der Organisation die auf den Zertifikaten angezeigt wird',
        ],
        [
            'key' => 'certificate_organization_sub_name',
            'value' => null,
            'description' => 'Untertitel der Organisation die auf den Zertifikaten angezeigt wird',
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->settings as $setting) {
            $model = Setting::firstOrNew([
                'key' => $setting['key'],
            ]);

            if (!$model->exists) {
                $model->setValue($setting['value']);
                $model->setDescription($setting['description']);
            }
            $model->save();
        }
    }
}
