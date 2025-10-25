<?php

namespace App\Http\Controllers\Administration;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Requirements\CreateRequest;
use App\Models\Fractions\Fraction;
use App\Models\Qualifications\Qualification;
use Illuminate\Http\Request;
use App\Models\Qualifications\Requirement;

class RequirementsController extends Controller
{
    /**
     * Anzeige der Voraussetzungen einer Qualifikation
     *
     * @return mixed
     */
    public function show(Request $request, Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.edit');
        $qualification->load(['requirements' => function ($query) {
            $query->orderByDefault()
                ->with('fraction');
        }]);
        $requirements = $qualification->requirements->sortBy('fraction_id');
        $fractions = Fraction::get();
        return view('administration.requirements.show', compact(
            'qualification',
            'fractions',
            'requirements'
        ));
    }

    /**
     * Legt eine neue Voraussetzung für eine Qualifikation an
     *
     * @param \App\Http\Requests\Administration\Requirements\CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request, Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.edit');
        $requirement = new Requirement();
        $requirement->setName($request->input('name'));
        $requirement->setFractionId($request->input('fraction_id'));
        $requirement->setQualificationId($qualification->getId());
        $requirement->setRank($request->input('rank', 0));
        $requirement->save();

        Alert::addAlert(__('general.erfolgreich_angelegt'), 'success');
        return redirect()->back();
    }

    /**
     * Seite zum bearbeiten einer Voraussetzung
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Qualifications\Qualification $qualification
     * @param \App\Models\Qualifications\Requirement $requirement
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, Qualification $qualification, Requirement $requirement)
    {
        $this->checkPermission('administration.qualifications.edit');
        $requirement->load('fraction');
        $fractions = Fraction::get();

        return view('administration.requirements.edit', compact(
            'qualification',
            'requirement',
            'fractions'
        ));
    }

    /**
     * Update einer Voraussetzung
     *
     * @param \App\Http\Requests\Administration\Qualifications\CreateRequest $request
     * @param \App\Models\Qualifications\Qualification $qualification
     * @param \App\Models\Qualifications\Requirement $requirement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateRequest $request, Qualification $qualification, Requirement $requirement)
    {
        $this->checkPermission('administration.qualifications.edit');

        $requirement->setName($request->input('name'));
        $requirement->setFractionId($request->input('fraction_id'));
        $requirement->setQualificationId($qualification->getId());
        $requirement->setRank($request->input('rank', 0));
        $requirement->save();

        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');
        return redirect()->route('administration.requirements.show', $qualification);
    }

    /**
     * Löscht eine Voraussetzung
     *
     * @param \App\Models\Qualifications\Requirement $requirement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Qualification $qualification, Requirement $requirement)
    {
        $this->checkPermission('administration.requirements.delete');

        $requirement->delete();

        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');
        return redirect()->back();
    }
}
