<?php

namespace App\Http\Controllers;

use App\Actions\StoreTrainingRequestAction;
use App\DTO\TrainingRequestViewData;
use App\Facades\Alert;
use App\Http\Requests\StoreTrainingRequestRequest;
use App\Models\Trainings\Request as TrainingRequest;
use Illuminate\Support\Collection;

class TrainingRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $data = TrainingRequest::query()
            ->with(['user.account.fractions', 'qualification'])
            ->isDateInFuture()
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->map(fn(TrainingRequest $item) => TrainingRequestViewData::fromModel($item))
            ->groupBy('date')
            ->map(function (Collection $items) {
                return $items->groupBy(fn(TrainingRequestViewData $view_data) => $view_data->qualification->label);
            });
        // dd($data);

        return view('training.request.index', [
            'data' => $data,
        ]);
    }

    /**
     * Ausbildungswunsch Anfragen
     *
     * @param  \Illuminate\Http\Request                $request
     * @param  TrainingRequestAction                   $training_request_action
     * @return mixed|\Illuminate\Http\RedirectResponse
     */
    public function store(StoreTrainingRequestRequest $request, StoreTrainingRequestAction $store_training_request_action)
    {

        $action = $store_training_request_action->execute($request->safe()->toArray());

        Alert::addAlert($action->message, $action->success ? 'success' : 'danger');

        return redirect()->back();
    }
}
