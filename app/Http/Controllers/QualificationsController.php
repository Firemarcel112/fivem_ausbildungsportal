<?php

namespace App\Http\Controllers;

use Exception;
use App\Facades\Alert;
use App\Models\DocumentLink;
use App\Models\Qualifications\Qualification;
use App\Models\User;
use App\Models\User\Qualification as UserQualification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QualificationsController extends Controller
{

    /**
     * Weißt dem User eine Qualifikation zu
     *
     * @param Request $request
     * @param User $user
     */
    public function assign(Request $request, User $user)
    {
        $this->checkPermission('user.qualifications.assign');

        $qualification = Qualification::findOrFail($request->qualification_id);

        $user_qualification = UserQualification::firstOrNew([
            'qualification_id' => $request->qualification_id,
            'user_id' => $user->getId(),
        ]);
        $user_qualification->setCreated(Carbon::create($request->date));
        $user_qualification->save();


        return redirect()->to(route('usermanagement.index') . "?search=" . $user->getFullName());
    }

    /**
     * Entfernt die Qualifikation und die Dokumentzuordnung
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function remove(Request $request, User $user)
    {
        $this->checkPermission('user.qualifications.remove');

        $qualification_ids = $request->input('qualifications');
        $qualifications = Qualification::whereIn('qualification_id', $qualification_ids)->get();
        /** @var Qualification $qualification */
        foreach ($qualifications as $qualification) {
            try {
                $document = $user->account->certificates->where('title', 'LIKE', 'Zertifikat: ' . $qualification->getName())->first();
                $document_id = $document->getId();
                DocumentLink::firstWhere('document_id', $document_id)?->delete();

                $user_qualification = $user->account->directQualifications->where('qualification_id', $qualification->getId())
                    ->first();
                $user_qualification?->delete();
            } catch (Exception $e) {
                Alert::addAlert('Qualifikation konnte nicht entfernt werden', 'danger');
                return redirect()->back();
            }
        }

        Alert::addAlert('Qualifikation entfernt', 'success');
        return redirect()->to(route('usermanagement.index') . "?search=" . $user->getFullName());
    }
}
