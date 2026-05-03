<?php

namespace Database\Seeders\Test;

use App\Models\Fraction;
use Illuminate\Database\Seeder;

class AddFraction extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Fraction::create([
            'name' => fake()->company(),
            'short_name' => fake()->randomLetter(),
            'master' => 1,
        ]);
    }
}
