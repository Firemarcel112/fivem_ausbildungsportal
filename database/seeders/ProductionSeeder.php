<?php

namespace Database\Seeders;

use Arr;
use Database\Seeders\Production\AddFractions;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Database\Seeders\Production\AddPermissions;
use Database\Seeders\Production\AddQualifications;

class ProductionSeeder extends Seeder
{

    public array $classes = [
        AddFractions::class,
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
