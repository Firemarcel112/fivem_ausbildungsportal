<?php

namespace App\Http\Controllers;

use App\Enums\Training\Type;
use App\Events\DiscordNotify;
use App\Facades\Alert;
use App\Models\Ausbildung;
use App\Models\Fractions\Fraction;
use App\Models\Trainings\Participant;
use App\Models\Trainings\Request as TrainingRequest;
use App\Models\Trainings\Training;
use App\Models\User;
use App\Models\User\Account;
use App\Traits\DiscordTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    use DiscordTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('trainings.show');

        $trainings = Training::with([
            'participants.account',
            'trainer.account',
            'qualification',
        ])
            ->isCompletedBuilder(0)
            ->orderByDefault()
            ->get();

        $trainings_completed = Training::with([
            'participants.account',
            'trainer.account',
            'qualification',
        ])
            ->isCompletedBuilder(1)
            ->orderByDefault('DESC')
            ->paginate(20);

        return view('ausbilder.overview', [
            'trainings' => $trainings,
            'trainings_completed' => $trainings_completed,
            'algorithmen_kategorien' => collect(),
        ]);
    }

    /**
     * Summary of store
     *
     * @param  \Illuminate\Http\Request          $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        if (!Auth::check() && !Auth::user()->can('trainings.show')) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }

        $request->flash();
        if (empty($request->get('trainer_id'))) {
            Alert::addAlert(__('general.bitte_waehle_einen_ausbilder_aus'), 'danger');

            return redirect()->back();
        }
        $time = SupportCarbon::createFromFormat('Y-m-d H:i', $request->input('date') . ' ' . $request->input('time'));
        if ($time <= now()->addMinutes((int) config('settings.training_creation_time_limit'))) {
            Alert::addAlert(__('general.ausbildung_kann_nicht_in_vergangenheit_liegen'), 'danger');

            return redirect()->back();
        }
        $model = new Training;
        $model->setQualificationId($request->input('qualification_id'));
        $model->setTrainerId($request->input('trainer_id'));
        $model->setMeetingPoint($request->input('meeting_point'));
        $model->setDate(Carbon::parse($request->input('date')));
        $model->setTime(Carbon::parse($request->input('time')));
        $model->setAdditionalInformation($request->input('infos') ?? '');
        $model->setMaxParticipants($request->input('max_participants'));
        $model->setMinParticipants($request->input('min_participants'));

        $notifications = $request->input('discord_notification') ?? [];

        if ($model->save()) {
            $webhook_urls = [];
            if (!empty($notifications)) {
                if (in_array('alle', $notifications)) {
                    $webhook_urls = Fraction::pluck('discord_webhook')
                        ->filter()
                        ->toArray();
                } else {
                    $webhook_urls = Fraction::isInFractionId($notifications)->pluck('discord_webhook')->filter()->toArray();
                }
            }

            if (!empty($webhook_urls)) {
                event(new DiscordNotify(
                    $webhook_urls,
                    Type::TRAINING_CREATED,
                    $model,
                ));
            }

            Alert::addAlert(__('general.ausbildung_erfolgreich_angelegt'), 'success');

            return redirect()->back();
        }
        Alert::addAlert(__('general.fehler_bei_der_erstellung'), 'danger');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {
        $training->load(['trainer', 'participants.account.fractions']);
        if (!Auth::user()->can('trainings.show')) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }

        $trainers = User::with(['permissions', 'account'])
            ->withIsTrainer()
            ->get();

        $users_in_training = $training->participants
            ->pluck('user_id')
            ->toArray();

        $users_not_in_training = User::with(['account.fractions', 'discord'])
            ->whereNotIn('id', $users_in_training)
            ->get()
            ->sortBy(function ($user) {
                return $user->account->getLastName();
            })
            ->sortBy(function ($user) {
                return $user->account->getDefaultFractionId();
            });

        return view('ausbildungen.overview', [
            'training' => $training,
            'participants' => $training->getSortParticipants(),
            'trainers' => $trainers,
            'users_not_in_training' => $users_not_in_training,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request  $request
     * @param Training $training
     */
    public function update(Request $request, Training $training)
    {
        if (!Auth::user()->isTrainer()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $request->flash();
        $trainer = $request->input('trainer_id');
        $date = $request->input('date');
        $time = $request->input('time');
        $min_participants = $request->input('min_participants');
        $max_participants = $request->input('max_participants');
        $meeting_point = $request->input('meeting_point');
        $additional_informations = $request->input('additional_informations');

        if (empty($trainer)) {
            Alert::addAlert(__('general.ausbilder_auswaehlen'), 'danger');

            return redirect()->back();
        }

        $time = SupportCarbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);
        if ($time <= now()->addMinutes((int) config('settings.training_creation_time_limit'))) {
            Alert::addAlert(__('general.ausbildung_kann_nicht_in_vergangenheit_liegen'), 'danger');

            return redirect()->back();
        }

        $old_values = $training->only([
            'trainer_id',
            'date',
            'time',
            'min_participants',
            'max_participants',
            'meeting_point',
            'additional_informations',
        ]);

        $training->setTrainerId($trainer);
        $training->setDate(Carbon::parse($date));
        $training->setTime(Carbon::parse($time));
        $training->setMinParticipants($min_participants);
        $training->setMaxParticipants($max_participants);
        $training->setMeetingPoint($meeting_point);
        $training->setAdditionalInformation($additional_informations);

        if ($training->save()) {
            $new_values = $training->only(
                'trainer_id',
                'date',
                'time',
                'min_participants',
                'max_participants',
                'meeting_point',
                'additional_informations'
            );

            $webhook_urls = $this->getFractionsWebhookUrls($request->get('discord_notification') ?? []);

            if (!empty($webhook_urls)) {
                event(new DiscordNotify(
                    $webhook_urls,
                    Type::TRAINING_UPDATED,
                    $training,
                    [
                        'old_values' => $old_values,
                        'new_values' => $new_values,
                    ],
                ));
            }

            Alert::addAlert(__('general.ausbildung_erfolgreich_geaendert'), 'success');

            return redirect()->back();
        }
        Alert::addAlert(__('general.error'), 'danger');

        return redirect()->back();
    }

    /**
     * Schließt die Ausbildung ab
     *
     * @param Request    $request
     * @param Ausbildung $ausbildung
     */
    public function complete(Request $request, Training $training)
    {
        if (!Auth::user()->isTrainer()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $fraktionen = Fraction::get();

        $notifications = [];
        foreach ($fraktionen as $fraktion) {
            if (empty($fraktion->getDiscordWebhookCompleted())) {
                continue;
            }
            $notifications[] = $fraktion->getDiscordWebhookCompleted();
        }

        if ($request->has('cancelled')) {
            $training->setCanceled(1);
            $training->setCompleted(1);
            if ($training->save()) {
                event(new DiscordNotify(
                    $notifications,
                    Type::TRAINING_COMPLETED,
                    $training,
                    [
                        'cancelled' => true,
                        'cancelled_notice' => $request->input('cancelled_notice'),
                    ]
                ));
                Alert::addAlert(__('general.ausbildung_abgeschlossen'), 'success');

                return redirect()->route('ausbilder.index');
            }
        }

        $participants = $training->getSortParticipants();
        $participants_data = $participants->mapWithKeys(function ($participant) {
            return [
                $participant->getId() => [
                    'id' => $participant->getId(),
                    'name' => $participant->getFullName(),
                    'fraction' => $participant->account->getDefaultFraction()->short_name,
                ],
            ];
        });
        $training->setCompleted(1);

        if ($training->save()) {
            event(new DiscordNotify(
                $notifications,
                Type::TRAINING_COMPLETED,
                $training,
                [
                    'participants_data' => $participants_data,
                    'participants' => $participants,
                    'fractions' => $fraktionen,
                    'request' => $request->all(),
                ],
            ));

            Alert::addAlert(__('general.ausbildung_abgeschlossen'), 'success');

            return redirect()->route('ausbilder.index');
        }
        Alert::addAlert('Es ist ein Fehler aufgetreten', 'error');

        return redirect()->back();
    }

    /**
     * Fügt Teilnehmer zur Ausbildung hinzu
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \App\Models\Trainings\Training    $training
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addParticipants(Request $request, Training $training)
    {
        if (!Auth::user()->isTrainer()) {
            Alert::addAlert(__('general.keine_berechtigung'), 'danger');

            return redirect()->back();
        }
        $success = false;
        foreach ($request->input('user_ids', []) as $user_id) {
            $user = Account::find($user_id);
            if (!$user) {
                Alert::addAlert(__('general.teilnehmer_nicht_gefunden', [
                    'name' => $user?->getFullName() ?? __('general.unbekannt'),
                ]), 'danger');

                continue;
            }

            $model = Participant::firstOrNew([
                'training_id' => $training->getId(),
                'user_id' => $user_id,
            ]);
            if ($model->exists) {
                Alert::addAlert(__('general.teilnehmer_bereits_angemeldet', [
                    'name' => $user->getFullName(),
                ]), 'warning');

                continue;
            }

            if ($model->save()) {
                $success = true;
            }
        }

        if ($success) {
            Alert::addAlert('Teilnehmer zum Lehrgang hinzugefügt', 'success');
        }

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
        if ($training->participants && $training->participants->count() > 0) {
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
     * @param  Request    $request
     * @param  Ausbildung $training
     * @return mixed
     */
    public function register(Request $request, Training $training)
    {
        $model = Participant::firstOrNew([
            'training_id' => $training->getId(),
            'user_id' => Auth::user()->account->getId(),
        ]);

        if ($model->exists) {
            Alert::addAlert(__('general.bereits_zum_lehrgang_angemeldet'), 'warning');

            return redirect()->back();
        }
        if ($model->save()) {
            Alert::addAlert(
                __('general.anmeldung_lehrgang_erfolgreich', [
                    'name' => Auth::user()->getFullName(),
                    'training' => $training->getName(),
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
     * @param  Request    $request
     * @param  Ausbildung $Ausbildung
     * @return mixed
     */
    public function signOut(Request $request, Training $training)
    {
        if ($training->isCompleted()) {
            Alert::addAlert('Ausbildung bereits abgeschlossen', 'danger');

            return redirect()->back();
        }

        $participant = Participant::isTrainingId($training->getId())
            ->isUserId(Auth::user()->account->getId())
            ->first();

        if ($participant) {
            $participant->delete();
            Alert::addAlert(__('general.abmeldung_lehrgang_erfolgreich'), 'success');
        } else {
            Alert::addAlert(__('general.teilnehmer_nicht_gefunden'), 'danger');
        }

        return redirect()->back();
    }

    /**
     * Ausbildungswunsch Anfragen
     *
     * @param  \Illuminate\Http\Request                $request
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function request(Request $request)
    {
        $qualification_id = $request->qualification_id;
        $date = $request->date;
        $time = $request->time;

        if (empty($qualification_id) || empty($date) || empty($time)) {
            Alert::addAlert(__('general.fehler'), 'danger');

            return redirect()->back();
        }

        $model = new TrainingRequest;
        $model->setUserId(Auth::user()->getId());
        $model->setQualificationId($qualification_id);
        $model->setDate(Carbon::parse($date));
        $model->setTime(Carbon::parse($time));
        $model_exists = TrainingRequest::where('user_id', Auth::user()->getId())
            ->where('qualification_id', $qualification_id)
            ->where('date', $model->getDate()->format('Y-m-d'))
            ->where('time', $model->getTime()->format('H:i'))
            ->first();

        if (!is_null($model_exists)) {
            Alert::addAlert(__('general.bereits_angefragt'), 'success');

            return redirect()->back();
        }

        if ($model->save()) {
            Alert::addAlert(__('general.erfolgreich_gespeichert'), 'success');
        } else {
            Alert::addAlert(__('general.fehler'), 'danger');
        }

        return redirect()->back();
    }
}
