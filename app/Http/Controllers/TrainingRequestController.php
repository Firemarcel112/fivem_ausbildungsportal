<?php

namespace App\Http\Controllers;

use App\Models\Trainings\Request as TrainingRequest;
use Illuminate\Http\Request;

class TrainingRequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:ausbilder');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $model = TrainingRequest::with(['user.account.fractions', 'qualification'])
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $grouped = [];
        $model->each(function ($item) use (&$grouped) {
            $qualification_name = $item->qualification->getName();
            $date = \Carbon\Carbon::parse("{$item->getDate()->format('d.m.Y')} {$item->getTime()->format('H:i')}")->format('d.m.Y H:i');
            if (!isset($grouped[$date])) {
                $grouped[$date][$qualification_name] = [
                    'users' => [],
                ];
            }
            $grouped[$date][$qualification_name]['users'][] = [
                'id' => $item->user->id,
                'name' => $item->user->getFullName() . ' (' . $item->user->account->getDefaultFraction()->getShortName() . ')',
            ];
        });

        return view('training.request.index', [
            'data' => $grouped,
        ]);
    }
}
