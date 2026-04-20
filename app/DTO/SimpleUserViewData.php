<?php

namespace App\DTO;

use App\Enums\Gender;
use App\Models\Qualification;
use App\Models\User;
use App\Models\User\Account;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

readonly class SimpleUserViewData
{
    public function __construct(
        public int $id,
        public int $account_id,
        public string $username,
        public string $salutation,
        public string $full_name,
        public string $first_name,
        public string $last_name,
        public string $initials,
        public Gender $gender,
        public Carbon $date_of_birth,
        public ?string $birth_location,
        public array $fraction_data,
        public bool $is_trainer,
        public Collection $qualifications,
        public string $created_at,
        public ?SimpleListItem $training_ban_data = null,

    ) {}

    public static function fromModel(?User $user): ?self
    {
        if (empty($user)) {
            return null;
        }
        $user->loadMissing('account.fractions');

        $fraction_data = [
            'default' => [
                'id' => $user->account->getDefaultFractionId(),
                'short_name' => $user->account->getDefaultFraction()->short_name,
                'name' => $user->account->getDefaultFraction()->name,
            ],
            'additional' => [],
        ];

        foreach ($user->account->fractions as $fraction) {
            if ($fraction->pivot->default) {
                continue;
            }
            $fraction_data['additional'][] = [
                'id' => $fraction->getKey(),
                'short_name' => $fraction->short_name,
                'name' => $fraction->name,
            ];
        }
        $training_ban_data = null;

        $qualifications = $user->account->qualifications
            ->map(function (Qualification $item) {
                /** @var \App\Models\User\Qualification $pivot */
                $pivot = $item->pivot;

                return new SimpleListItem(
                    $item->getKey(),
                    $item->name,
                    $pivot->created_at->format('d.m.Y'),
                    [
                        'training_id' => $pivot->training_id,
                    ]
                );
            });

        if ($user->activeTrainingBan?->count() > 0) {
            $training_ban = $user->activeTrainingBan;

            $training_ban_data = new SimpleListItem(
                0,
                $training_ban->issuer_name,
                nl2br($training_ban->reason),
                [
                    'date_from' => $training_ban->date_from->format('d.m.Y'),
                    'date_to' => $training_ban->date_to->format('d.m.Y'),
                ],
            );
        }

        return new self(
            $user->getKey(),
            $user->account->getKey(),
            $user->name,
            $user->account->salutation,
            $user->account->full_name,
            $user->account->first_name,
            $user->account->last_name,
            $user->account->initials,
            $user->account->gender,
            $user->account->date_of_birth,
            $user->account->birth_location,
            $fraction_data,
            $user->isTrainer(),
            $qualifications,
            $user->created_at->format('d.m.Y'),
            $training_ban_data,
        );
    }

    public static function fromAccountModel(?Account $account): ?self
    {
        if (empty($account)) {
            return null;
        }
        $account->loadMissing('fractions');

        $fraction_data = [
            'default' => [
                'id' => $account->getDefaultFractionId(),
                'short_name' => $account->getDefaultFraction()->short_name,
                'name' => $account->getDefaultFraction()->name,
            ],
            'additional' => [],
        ];

        foreach ($account->fractions as $fraction) {
            if ($fraction->pivot->default) {
                continue;
            }
            $fraction_data['additional'][] = [
                'id' => $fraction->getKey(),
                'short_name' => $fraction->short_name,
                'name' => $fraction->name,
            ];
        }

        $qualifications = $account->qualifications
            ->map(function (Qualification $item) {
                /** @var \App\Models\User\Qualification $pivot */
                $pivot = $item->pivot;

                return new SimpleListItem(
                    $item->getKey(),
                    $item->name,
                    $pivot->created_at->format('d.m.Y'),
                    [
                        'training_id' => $pivot->training_id,
                    ]
                );
            });

        $training_ban_data = null;

        if ($account->user->activeTrainingBan?->count() > 0) {
            $training_ban = $account->user->activeTrainingBan;

            $training_ban_data = new SimpleListItem(
                0,
                $training_ban->issuer_name,
                nl2br($training_ban->reason),
                [
                    'date_from' => $training_ban->date_from->format('d.m.Y'),
                    'date_to' => $training_ban->date_to->format('d.m.Y'),
                ],
            );
        }

        return new self(
            $account->user->getKey(),
            $account->getKey(),
            '',
            $account->salutation,
            $account->full_name,
            $account->first_name,
            $account->last_name,
            $account->initials,
            $account->gender,
            $account->date_of_birth,
            $account->birth_location,
            $fraction_data,
            $account->user->isTrainer(),
            $qualifications,
            $account->user->created_at,
            $training_ban_data,
        );
    }
}
