<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AddDefaultSettings extends Seeder
{
    /**
     * Permissions mit Namen die erstellt werden sollen
     *
     * @var array<int, array<string, string|int|null>>
     */
    public array $settings = [
        [
            'key' => 'default_meeting_point',
            'value' => '',
            'description' => 'Standard Treffpunkt für Ausbildungen',
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
        ],
        [
            'key' => 'training_creation_time_limit',
            'value' => 1440,
            'description' => 'Zeit in Minuten bevor die Ausbildung beginnt, bis zu der diese erstellt werden darf',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->settings as $setting) {

            /**
             * @var string $key
             */
            $key = $setting['key'];
            /**
             * @var string|null $value
             */
            $value = $setting['value'];
            /**
             * @var string $description
             */
            $description = $setting['description'];

            $model = Setting::firstOrNew([
                'key' => $key,
            ]);

            if (!$model->exists) {
                $model->value = $value ?? '';
                $model->description = $description;
            }
            $model->save();
        }
    }
}
