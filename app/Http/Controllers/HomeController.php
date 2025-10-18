<?php

namespace App\Http\Controllers;


use App\Models\Fractions\Fraction;
use App\Models\Qualifications\Qualification;
use App\Models\Trainings\Training;
use Illuminate\Support\Facades\Gate;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $is_trainer = Gate::check('is_trainer');

        $trainings = Training::with([
            'trainer.account',
            'qualification',
            'participants.account',
            'requirements',
        ])
            ->when(!$is_trainer, function ($query) {
                $query->isAvailable();
            })
            ->isCompletedBuilder(0)
            ->orderByDefault()
            ->get()
            ->groupBy('date');

        $fractions = Fraction::get();

        $qualifications = Qualification::isOrderByDefault()
            ->get();

        return view('home', [
            'trainings' => $trainings,
            'fractions' => $fractions,
            'qualifications' => $qualifications,
        ]);
    }
}
