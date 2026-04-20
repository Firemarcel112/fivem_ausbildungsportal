<?php

namespace Database\Seeders;

use Database\Seeders\Production\AddPermissions;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * @var array<string>
     */
    public array $classes = [
        AddPermissions::class,

        AddDefaultSettings::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        foreach ($this->classes as $class) {
            $this->call($class);
        }
    }
}
