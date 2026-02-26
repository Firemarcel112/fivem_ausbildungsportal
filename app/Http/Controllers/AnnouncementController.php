<?php

namespace App\Http\Controllers;

use App\Facades\Alert;
use App\Traits\DiscordTrait;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    use DiscordTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->flash();
        $roles = array_filter(array_map('trim', explode(',', preg_replace('/\s+/', '', $request->get('roles')))));
        if (!empty($roles)) {
            $content = '';
            foreach ($roles as $role) {
                $content .= "<@&{$role}>\n";
            }
        }
        $title = 'Ankündigung';
        if (!empty($request->get('title'))) {
            $title = 'Ankündigung: ' . $request->get('title');
        }
        if (empty($request->get('text'))) {
            Alert::addAlert('Fehler beim versenden der Ankündigung: Kein Text gesetzt', 'error');

            return redirect()->back();
        }
        $embed = $this->buildEmbed(
            title: $title,
            description: $request->get('text'),
            content: $content ?? ''
        );

        $webhook_urls = $this->getFractionsWebhookUrls($request->get('discord_notification') ?? []);

        foreach ($webhook_urls as $webhook_url) {
            if (empty($webhook_url)) {
                continue;
            }
            $this->sendToDiscord($webhook_url, $embed);
        }
        Alert::addAlert('Ankündigung gesendet', 'success');

        return redirect()->back();
    }
}
