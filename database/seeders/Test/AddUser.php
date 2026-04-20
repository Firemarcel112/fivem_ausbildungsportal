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
        $fractions = Fraction::get();

        $count = 10;

        for ($count; $count > 0; $count--) {
            $model = User::create([
                'name' => fake()->userName(),
                'password' => 'password',
            ]);

            $account_model = Account::create([
                'user_id' => $model->getKey(),
                'first_name' => fake()->firstName(),
                'last_name' => fake()->lastName(),
                'date_of_birth' => fake()->date(),
            ]);

            $fraction_model = UserFraction::create([
                'user_id' => $account_model->getKey(),
                'fraction_id' => $fractions->random()->getKey(),
                'default' => true,
            ]);

            $additional_fraction = fake()->boolean(50);

            if ($additional_fraction) {
                $fraction_id = $fractions->random()->getKey();
                while ($fraction_id == $fraction_model->fraction_id) {
                    $fraction_id = $fractions->random()->getKey();
                }
                UserFraction::create([
                    'user_id' => $account_model->getKey(),
                    'fraction_id' => $fraction_id,
                    'default' => false,
                ]);
            }
        }
    }
}
