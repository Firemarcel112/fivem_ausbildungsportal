<?php

namespace Database\Seeders;

use App\Models\Ausbildung;
use Database\Seeders\Test\AddUser;
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
        $this->call(AddUser::class);
    }
}
