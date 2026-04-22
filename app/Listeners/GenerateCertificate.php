<?php

namespace App\Listeners;

use App\DTO\SimpleUserViewData;
use App\DTO\TrainingParticipantViewData;
use App\Events\TrainingCompleted;
use App\Jobs\CreateCertificate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;

class GenerateCertificate implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(TrainingCompleted $event): void
    {
        if (!$event->model->qualification?->generate_certificate) {
            return;
        }

        $participants = $this->filterForCertificate($event->getGroupedParticipants());

        $model_trainer = $event->model->trainer;

        /**
         * @var \App\Models\Qualification $qualification
         */
        $qualification = $event->model->qualification;

        /**
         * @var SimpleUserViewData $trainer
         */
        $trainer = SimpleUserViewData::fromModel($model_trainer);

        foreach ($participants as $participant) {
            CreateCertificate::dispatch(
                $participant,
                $trainer,
                $qualification,
                $event->model->date->format('d.m.Y'),
                $participant->account_id,
            );
        }
    }

    /**
     * @param  array<mixed>                                 $grouped_participants
     * @return Collection<int, TrainingParticipantViewData>
     */
    protected function filterForCertificate(array $grouped_participants): Collection
    {
        /** @var Collection<int, mixed> $grouped_participants_passed */
        $grouped_participants_passed = $grouped_participants['passed'];

        if ($grouped_participants_passed->isEmpty()) {
            return collect([]);
        } else {
            return $grouped_participants_passed->flatten();
        }
    }
}
