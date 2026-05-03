<?php

namespace Database\Seeders;

use Database\Seeders\Test\AddUser;
use Database\Seeders\Test\AddFraction;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('app.env') == 'production') {
            return;
        }
        $this->call(ProductionSeeder::class);
        $this->call(AddFraction::class);
        $this->call(AddUser::class);
    }
}
