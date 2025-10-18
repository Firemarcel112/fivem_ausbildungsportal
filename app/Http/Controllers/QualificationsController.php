<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Facades\Alert;
use App\Models\Qualifications\Qualification;
use App\Models\Qualifikation;
use App\Models\User\Qualification as UserQualification;
use Carbon\Carbon;

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

        Qualification::findOrFail($request->qualification_id);

        $user_qualification = UserQualification::firstOrNew([
            'qualification_id' => $request->qualification_id,
            'user_id' => $user->getId(),
        ]);
        $user_qualification->setCreated(Carbon::create($request->date));
        $user_qualification->save();

        return redirect()->to(route('usermanagement.index') . "?search=" . $user->getFullName());
    }
}
