<?php

namespace App\Http\Controllers\Ausbilder;

use App\Enums\Role;
use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\AlgorithmenKategorie;
use App\Models\Ausbildung;
use App\Models\Fractions\Fraction;
use App\Models\Fraktion;
use App\Models\Qualifications\Qualification;
use App\Models\Qualifikation;
use App\Models\Trainings\Training;
use App\Models\User;

class OverviewController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->checkPermission('is_trainer');

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
}
