<?php

namespace App\Http\Controllers\Administration;

use App\Facades\Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests\Administration\Fractions\CreateRequest;
use App\Models\Fractions\Fraction;
use App\Models\User\Fraction as UserFraction;
use Illuminate\Http\Request;

class FractionsController extends Controller
{
    /**
     * Anzeige der Fraktionen
     *
     * @return mixed
     */
    public function index()
    {
        $this->checkPermission('administration.fractions.edit');
        $fractions = Fraction::get();
        return view('administration.fractions.index', compact('fractions'));
    }

    /**
     * Legt eine neue Fraktion an
     *
     * @param \App\Http\Requests\Administration\Fractions\CreateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateRequest $request)
    {
        $fraction = new Fraction();
        $fraction->setName($request->input('name'));
        $fraction->setShortName($request->input('short_name'));
        $fraction->setDiscordWebhook($request->input('discord_webhook'));
        $fraction->setDiscordWebhookCompleted($request->input('discord_webhook_completed'));
        $fraction->setMaster($request->has('master') ? 1 : 0);
        $fraction->save();

        Alert::addAlert(__('general.erfolgreich_angelegt'), 'success');
        return redirect()->back();
    }

    /**
     * Seite zum bearbeiten einer Fraktion
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Fractions\Fraction $fraction
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, Fraction $fraction)
    {
        $this->checkPermission('administration.fractions.edit');

        return view('administration.fractions.edit', compact('fraction'));
    }

    /**
     * Update einer Fraktion
     *
     * @param \App\Http\Requests\Administration\Fractions\CreateRequest $request
     * @param \App\Models\Fractions\Fraction $fraction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CreateRequest $request, Fraction $fraction)
    {
        $fraction->setName($request->input('name'));
        $fraction->setShortName($request->input('short_name'));
        $fraction->setDiscordWebhook($request->input('discord_webhook'));
        $fraction->setDiscordWebhookCompleted($request->input('discord_webhook_completed'));
        $fraction->setMaster($request->has('master') ? 1 : 0);
        $fraction->save();

        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');
        return redirect()->route('administration.fractions.index');
    }

    /**
     * Löscht eine Fraktion
     *
     * @param \App\Models\Fractions\Fraction $fraction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Fraction $fraction)
    {
        $this->checkPermission('administration.fractions.delete');

        $user_with_fraction_exists = UserFraction::where('fraction_id', $fraction->getFractionId())->exists();

        if ($user_with_fraction_exists) {
            Alert::addAlert(__('texte.administration.fraktion_nicht_loeschbar', [
                'name' => $fraction->getName()
            ]), 'danger');
            return redirect()->back();
        }

        $fraction->delete();

        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');
        return redirect()->back();
    }
}
