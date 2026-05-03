<?php

namespace Database\Seeders\Test;

use App\Models\Fraction;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use Illuminate\Database\Seeder;

class AddUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fractions = Fraction::query()
            ->get();

        $count = 10;

        $user_count = User::query()
            ->count('*');

        for ($i = 0; $i < $count; $i++) {

            $login_name = fake()->userName();
            if ($user_count == 0 && $i == 0) {
                $login_name = 'administrator';
            }

            $model = User::create([
                'name' => $login_name,
                'password' => 'password',
            ]);

            $account_model = Account::create([
                'user_id' => $model->getKey(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'date_of_birth' => fake()->date(),
            ]);

            UserFraction::create([
                'user_id' => $account_model->getKey(),
                'fraction_id' => $fractions->random()->getKey(),
                'default' => true,
            ]);
        }
    }
}
