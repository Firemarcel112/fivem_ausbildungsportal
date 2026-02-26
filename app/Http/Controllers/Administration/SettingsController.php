<?php

namespace App\Http\Controllers\Administration;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Anzeige der Einstellungen
     *
     * @param  Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $this->checkPermission('administration.settings.edit');

        $settings = Setting::all();

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Aktualisiert einen Eintrag
     *
     * @param  Request                           $request
     * @param  Setting                           $setting
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Setting $setting)
    {
        $this->checkPermission('administration.settings.edit');

        $validated = $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $setting->value = $validated['value'];
        $setting->save();

        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');

        return redirect()->back();
    }
}
