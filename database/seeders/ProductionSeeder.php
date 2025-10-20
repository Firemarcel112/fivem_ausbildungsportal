<?php

namespace Database\Seeders;

use Arr;
use Database\Seeders\Production\AddPermissions;
use Database\Seeders\Production\AddQualifications;
use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{

    public array $classes = [
        AddQualifications::class,
        AddPermissions::class,
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
