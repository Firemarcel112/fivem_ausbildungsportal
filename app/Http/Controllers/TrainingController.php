<?php

namespace App\Http\Controllers;

use App\Actions\StoreTrainingAction;
use App\Actions\StoreTrainingParticipantAction;
use App\Actions\UpdateTrainingAction;
use App\DTO\SimpleUserViewData;
use App\DTO\TrainingParticipantViewData;
use App\DTO\TrainingViewData;
use App\Events\TrainingCompleted;
use App\Events\TrainingCreated;
use App\Events\TrainingUpdated;
use App\Facades\Alert;
use App\Http\Requests\CompletedTrainingRequest;
use App\Http\Requests\StoreTrainingParticipantsRequest;
use App\Http\Requests\StoreTrainingRequest;
use App\Http\Requests\UpdateTrainingRequest;
use App\Models\Ausbildung;
use App\Models\Training;
use App\Models\Trainings\Participant;
use App\Models\User;
use App\Models\User\Account;
use App\Traits\DiscordTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    use AuthorizesRequests;
    use DiscordTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Training::class);

        $trainings = Training::getUpcomingTrainings(false)
            ->map(fn(Training $item) => TrainingViewData::fromModel($item));

        return view('ausbilder.overview', compact('trainings'));
    }

    /**
     * Abgeschlossene Ausbildungen
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function completed()
    {
        $trainings = Training::with([
            'participants.account',
            'trainer.account',
            'qualification',
        ])
            ->isCompleted()
            ->orderByDefault('DESC')
            ->paginate(20)
            ->through(fn(Training $item) => TrainingViewData::fromModel($item));

        return view('ausbilder.trainings.completed', compact('trainings'));
    }

    /**
     * Summary of store
     *
     * @param  StoreTrainingRequest              $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTrainingRequest $request, StoreTrainingAction $store_training_action)
    {
        $this->authorize('create', Training::class);

        $store = $store_training_action->execute($request->safe()->except(['discord_notification']));

        if ($store->success) {
            /**
             * @var Training $model;
             */
            $model = $store->model;

            event(new TrainingCreated(
                $model,
                $request->collect('discord_notification')->flatten()->toArray(),
            ));

            Alert::success($store->message);
        } else {
            Alert::addAlert($store->message, 'danger');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {

        $this->authorize('view', $training);

        $training->load(['trainer', 'participants.account.fractions']);

        $users_in_training = $training->participants
            ->pluck('user_id')
            ->toArray();

        $users_not_in_training = Account::query()
            ->with(['fractions'])
            ->isNotInIds($users_in_training)
            ->get()
            ->sortBy(fn(Account $account) => $account->last_name)
            ->sortBy(fn(Account $account) => $account->default_fraction->getKey())
            ->map(fn(Account $account) => SimpleUserViewData::fromAccountModel($account));

        $participants = $training->getSortParticipants()
            ->map(fn(Participant $participant) => TrainingParticipantViewData::fromModel($participant, $training));

        $training = TrainingViewData::fromModel($training, 0);

        return view('ausbildungen.show', [
            'training' => $training,
            'participants' => $participants,
            'users_not_in_training' => $users_not_in_training,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTrainingRequest $request
     * @param Training              $training
     * @param UpdateTrainingAction  $update_training_action
     */
    public function update(UpdateTrainingRequest $request, Training $training, UpdateTrainingAction $update_training_action)
    {
        $this->authorize('update', $training);

        $request->flash();

        $original_values = $training->only([
            'trainer_id',
            'date',
            'time',
            'min_participants',
            'max_participants',
            'meeting_point',
            'additional_information',
        ]);

        $date = $request->input('date');
        $time = $request->input('time');

        $updated = $update_training_action->execute($training, $request->except('discord_notification'));

        /** @var Training $model */
        $model = $updated->model;

        event(new TrainingUpdated(
            $model,
            $original_values,
            $request->collect('discord_notification')->flatten()->toArray(),
        ));

        Alert::addAlert($updated->message, $updated->success ? 'success' : 'danger');

        return redirect()->back();
    }

    /**
     * Schließt die Ausbildung ab
     *
     * @param CompletedTrainingRequest $request
     * @param Training                 $training
     */
    public function complete(
        CompletedTrainingRequest $request,
        Training $training,
        UpdateTrainingAction $update_training_action
    ) {

        if ($request->input('cancelled') == 1) {
            $updated = $update_training_action->execute(
                $training,
                [
                    'cancelled' => 1,
                    'completed' => 1,
                ]
            );

            if ($updated->success) {

                /** @var Training $model */
                $model = $updated->model;

                event(new TrainingCompleted(
                    $model,
                    null,
                    [
                        'cancelled' => true,
                        'cancelled_notice' => $request->input('cancelled_notice'),
                    ],
                ));

                Alert::addAlert($updated->message, 'success');

                return redirect()->route('ausbilder.index');
            }
        }

        $participants = $training->getSortParticipants()
            ->map(fn(Participant $participant) => TrainingParticipantViewData::fromModel($participant, $training));

        $updated = $update_training_action->execute(
            $training,
            [
                'completed' => 1,
            ]
        );

        if (!$updated->success) {
            Alert::addAlert(__('general.fehler'), 'danger');

            return redirect()->back();
        }

        /** @var Training $model */
        $model = $updated->model;

        event(new TrainingCompleted(
            $model,
            $participants,
            $request->collect('participants')->toArray(),
        ));

        Alert::addAlert(__('general.ausbildung_abgeschlossen'), 'success');

        return redirect()->route('ausbilder.index');
    }

    /**
     * Fügt Teilnehmer zur Ausbildung hinzu
     *
     * @param  StoreTrainingParticipantsRequest  $request
     * @param  Training                          $training
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addParticipants(
        StoreTrainingParticipantsRequest $request,
        Training $training,
        StoreTrainingParticipantAction $store_participant_action,
    ) {
        $account_ids = $request->safe();

        foreach ($account_ids['account_ids'] as $account_id) {
            $saved = $store_participant_action->execute([
                'account_id' => $account_id,
                'training_id' => $training->getKey(),
            ]);
        }

        Alert::addAlert($saved->message, $saved->success ? 'success' : 'danger');

        return redirect()->back();
    }

    /**
     * Teilnehmer von der Ausbildung entfernen
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \App\Models\Trainings\Participant $participant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeParticipant(Request $request, Participant $participant)
    {
        if (!Auth::user()->isTrainer()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }

        if ($participant->delete()) {
            Alert::addAlert('Teilnehmer vom Lehrgang entfernt!', 'success');

            return redirect()->back();
        }

        Alert::addAlert('Kein Teilnehmer entfernt', 'error');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Training $training)
    {
        $this->checkPermission('trainings.delete');

        if ($training->isCompleted()) {
            Alert::addAlert(__('general.ausbildung_bereits_abgeschlossen'), 'danger');

            return redirect()->back();
        }

        $training->load('participants');
        if ($training->participants->count() > 0) {
            $training->participants->each(function ($participant) {
                $participant->delete();
            });
        }
        if ($training->delete()) {
            Alert::addAlert(__('general.ausbildung_geloescht'), 'success');

            return redirect()->back();
        }
    }

    /**
     * Sperrt einen Benutzer für Ausbildungen
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \App\Models\User                  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function ban(Request $request, User $user)
    {
        $this->checkPermission('trainingban.assign');
        $request->validate([
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
            'reason' => ['required', 'string'],
            'notice' => ['nullable', 'string'],
        ]);

        $ban = $user->banForTrainings(
            $request->input('reason'),
            $request->input('date_from'),
            $request->input('date_to'),
            $request->input('notice'),
        );

        if ($ban) {
            Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
        } else {
            Alert::addAlert(__('general.fehler'), 'danger');
        }

        return redirect()->back();
    }

    /**
     * Registrierung zur Ausbildung
     *
     * @param  Request  $request
     * @param  Training $training
     * @return mixed
     */
    public function register(Request $request, Training $training)
    {
        $model = Participant::firstOrNew([
            'training_id' => $training->getKey(),
            'user_id' => Auth::user()->account->getKey(),
        ]);

        if ($model->exists) {
            Alert::addAlert(__('general.bereits_zum_lehrgang_angemeldet'), 'warning');

            return redirect()->back();
        }
        if ($model->save()) {
            Alert::addAlert(
                __('general.anmeldung_lehrgang_erfolgreich', [
                    'name' => Auth::user()->account->full_name,
                    'training' => $training->name,
                ]),
                'success',
            );

            return redirect()->back();
        }

        return redirect()->back();
    }

    /**
     * Abmelden von der Ausbildung
     *
     * @param  Request  $request
     * @param  Training $training
     * @return mixed
     */
    public function signOut(Request $request, Training $training)
    {
        if ($training->isCompleted()) {
            Alert::addAlert('Ausbildung bereits abgeschlossen', 'danger');

            return redirect()->back();
        }

        $participant = Participant::isTrainingId($training->getId())
            ->isAccountId(Auth::user()->account->getId())
            ->first();

        if ($participant) {
            $participant->delete();
            Alert::addAlert(__('general.abmeldung_lehrgang_erfolgreich'), 'success');
        } else {
            Alert::addAlert(__('general.teilnehmer_nicht_gefunden'), 'danger');
        }

        return redirect()->back();
    }
}
