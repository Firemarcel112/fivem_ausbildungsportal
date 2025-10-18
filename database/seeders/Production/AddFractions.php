<?php

namespace Database\Seeders\Production;

use App\Models\Ausbildung;
use App\Models\Fractions\Fraction;
use Illuminate\Database\Seeder;

class AddFractions extends Seeder
{

    public array $fractions = [
        [
            'name' => 'Rettungsdienst',
            'short_name' => 'RD',
            'master' => 1,
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->fractions as $fraction) {
            $model = Fraction::firstOrNew([
                'name' => $fraction['name'],
            ]);
            $model->setShortName($fraction['short_name']);
            $model->setMaster($fraction['master'] ?? 0);
            $model->save();
        }
    }
}
