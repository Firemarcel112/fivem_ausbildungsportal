<?php

namespace App\Http\Controllers;

use App\Models\Fractions\Fraction;
use App\Models\Qualifications\Qualification;

class RequirementsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $qualifications = Qualification::with('requirements')
            ->isOrderByDefault()
            ->get();
        $fractions = Fraction::get();

        return view('requirements', [
            'qualifications' => $qualifications,
            'fractions' => $fractions,
        ]);
    }
}
