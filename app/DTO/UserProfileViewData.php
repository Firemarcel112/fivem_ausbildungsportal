<?php

namespace App\DTO;

use App\Models\Document;
use App\Models\Qualification;
use App\Models\User;
use Illuminate\Support\Collection;

readonly class UserProfileViewData
{
    /**
     * Summary of __construct
     *
     * @param int                        $id
     * @param string                     $salutation
     * @param string                     $full_name
     * @param string                     $date_of_birth
     * @param null|string                $birth_location
     * @param Collection<SimpleListItem> $qualifications
     * @param Collection<SimpleListItem> $certificates
     */
    public function __construct(
        public int $id,
        public string $salutation,
        public string $full_name,
        public string $initials,
        public string $date_of_birth,
        public ?string $birth_location,
        public Collection $qualifications,
        public Collection $certificates,
    ) {}

    public static function fromModel(User $user)
    {

        $qualifications = $user->account->qualifications
            ->map(function (Qualification $item) {
                /** @var \App\Models\User\Qualification $pivot */
                $pivot = $item->pivot;

                return new SimpleListItem(
                    $item->getKey(),
                    $item->name,
                    $pivot->created_at->format('d.m.Y'),
                );
            });

        $certificates = $user->account->certificates
            ->map(fn(Document $item) => new SimpleListItem(
                $item->getKey(),
                $item->title,
                $item->created_at->format('d.m.Y'),
            ));

        return new self(
            $user->getKey(),
            $user->account->salutation,
            $user->account->full_name,
            $user->account->initials,
            $user->account->date_of_birth->format('d.m.Y'),
            $user->account->birth_location,
            $qualifications,
            $certificates,
        );
    }
}
