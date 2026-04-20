<?php

namespace App\Http\Controllers\Administration;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Qualifications\CreateRequest;
use App\Models\Qualification;
use App\Models\Training;
use App\Models\User\Qualification as UserQualification;
use Illuminate\Http\Request;

class QualificationsController extends Controller
{
    /**
     * Anzeige der Qualifikationen
     *
     * @return mixed
     */
    public function index()
    {
        $this->checkPermission('administration.qualifications.edit');
        $qualifications = Qualification::with([
            'trainings',
            'userQualifications',
        ])
            ->isOrderByDefault()
            ->get();

        return view('administration.qualifications.index', compact('qualifications'));
    }

    /**
     * Legt eine neue Qualifikation an
     *
     * @param  \App\Http\Requests\Administration\Qualifications\CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $this->checkPermission('administration.qualifications.edit');
        $qualification = Qualification::create([
            'name' => $request->input('name'),
            'rank' => $request->input('rank', 0),
            'generate_certificate' => $request->input('generate_certificate', 1),
        ]);

        Alert::addAlert(__('general.erfolgreich_angelegt'), 'success');

        return redirect()->back();
    }

    /**
     * Seite zum bearbeiten einer Qualifikation
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  \App\Models\Qualification       $qualification
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.edit');

        return view('administration.qualifications.edit', compact('qualification'));
    }

    /**
     * Update einer Fraktion
     *
     * @param  \App\Http\Requests\Administration\Qualifications\CreateRequest $request
     * @param  \App\Models\Qualification                                      $qualification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateRequest $request, Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.edit');

        $qualification->update([
            'name' => $request->input('name'),
            'rank' => $request->input('rank', 0),
            'generate_certificate' => $request->input('generate_certificate', 1),
        ]);

        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');

        return redirect()->route('administration.qualifications.index');
    }

    /**
     * Löscht eine Qualifikation
     *
     * @param  \App\Models\Qualification         $qualification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.delete');
        $qualification->load('requirements');

        $exists_trainings = Training::where('qualification_id', $qualification->getKey())
            ->exists();
        $exists_user_with_qualification = UserQualification::isQualificationId($qualification->getKey())
            ->exists();

        if ($exists_trainings || $exists_user_with_qualification) {
            Alert::addAlert(__('texte.administration.qualifikation_nicht_loeschbar', [
                'name' => $qualification->name,
            ]), 'danger');

            return redirect()->back();
        }

        $qualification->requirements->each(function ($requirement) {
            $requirement->delete();
        });

        $qualification->delete();

        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');

        return redirect()->back();
    }

    /**
     * Blendet eine Qualifikation ein/aus
     *
     * @param  Qualification                     $qualification
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleHide(Qualification $qualification)
    {
        $this->checkPermission('administration.qualifications.edit');
        $qualification->update([
            'hide' => !$qualification->hide,
        ]);

        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');

        return redirect()->back();
    }
}
