<?php

namespace Database\Seeders\Production;

use App\Models\Fractions\Fraction;
use App\Models\Qualifications\Qualification;
use Illuminate\Database\Seeder;
use App\Models\Trainings\Requirement;

class AddQualifications extends Seeder
{

    public array $qualifications = [
        [
            'name' => 'Rettungshelfer',
            'rank' => 20,
            'requirements' => [
                [
                    'name' => 'Mindestens 3 Tage aktiv im Rettungsdienst',
                    'rank' => 10,
                    'fraction' => 'RD',
                ],
                [
                    'name' => 'Funklehrgang',
                    'rank' => 20,
                    'fraction' => 'RD',
                ],
            ],
        ],
        [
            'name' => 'Rettungssanitäter',
            'rank' => 30,
            'requirements' => [
                [
                    'name' => 'Rettungshelfer',
                    'rank' => 10,
                    'fraction' => 'RD',
                ],
                [
                    'name' => 'Mindestens 1 Woche als Rettungshelfer im Rettungsdienst tätig',
                    'rank' => 20,
                    'fraction' => 'RD',
                ],
            ],
        ],
        [
            'name' => 'Rettungsassistent',
            'rank' => 40,
            'requirements' => [
                [
                    'name' => 'Rettungssanitäter',
                    'rank' => 10,
                    'fraction' => 'RD',
                ],
                [
                    'name' => 'Mindestens 3 Wochen als Rettungssanitäter im Rettungsdienst tätig',
                    'rank' => 20,
                    'fraction' => 'RD',
                ]
            ]
        ],
        [
            'name' => 'Notfallsanitäter',
            'rank' => 50,
            'requirements' => [
                [
                    'name' => 'Rettungsassistent',
                    'rank' => 10,
                    'fraction' => 'RD',
                ],
                [
                    'name' => 'Mindestens 4 Wochen als Rettungsassistent im Rettungsdienst tätig',
                    'rank' => 20,
                    'fraction' => 'RD',
                ]
            ]
        ],
        [
            'name' => 'Funklehrgang',
            'rank' => 10,
            'requirements' => [
                [
                    'name' => 'Mitglied Rettungsdienst',
                    'rank' => 10,
                    'fraction' => 'RD',
                ]
            ]
        ]
    ];

    /**
     * Seed the application's database.
     * @todo Fehler in der Erstellung
     */
    public function run(): void
    {
        foreach ($this->qualifications as $qualification) {
            $model = Qualification::firstOrNew([
                'name' => $qualification['name'],
            ]);
            $model->setRank($qualification['rank']);
            $model->save();
            $model_id = $model->getId();
            if (!empty($qualification['requirements'])) {
                foreach ($qualification['requirements'] as $requirement) {
                    $fraction_id = Fraction::firstWhere('short_name', $requirement['fraction'])->getId();
                    $model = Requirement::firstOrNew([
                        'name' => $requirement['name'],
                        'qualification_id' => $model_id,
                        'fraction_id' => $fraction_id,
                    ]);
                    $model->setRank($requirement['rank']);
                    $model->save();
                }
            }
        }
    }
}
