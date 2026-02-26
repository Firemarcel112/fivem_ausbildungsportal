<?php

namespace Database\Seeders\Test;

use App\Models\Fractions\Fraction;
use App\Models\User;
use App\Models\User\Account;
use App\Models\User\Fraction as UserFraction;
use Carbon\Carbon;
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
            $model = new User;
            $model->setName(fake()->userName());
            $model->setPassword('password');
            $model->save();

            $account_model = new Account;
            $account_model->setUserId($model->id);
            $account_model->setFirstName(fake()->firstName());
            $account_model->setLastName(fake()->lastName());
            $account_model->setDateOfBirth(Carbon::create(fake()->date()));
            $account_model->save();

            $fraction_model = new UserFraction;
            $fraction_model->setUserId($account_model->getQueueableId());
            $fraction_model->setFractionId($fractions->random()->getKey());
            $fraction_model->setDefault(true);
            $fraction_model->save();
            $additional_fraction = fake()->boolean(50);

            if ($additional_fraction) {
                $fraction_id = $fractions->random()->getKey();
                while ($fraction_id == $fraction_model->fraction_id) {
                    $fraction_id = $fractions->random()->getKey();
                }
                $fraction_model = new UserFraction;
                $fraction_model->setUserId($account_model->getQueueableId());
                $fraction_model->setFractionId($fraction_id);
                $fraction_model->setDefault(false);
                $fraction_model->save();
            }
        }
    }
}
