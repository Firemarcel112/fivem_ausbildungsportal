<?php

namespace App\Interfaces;

interface DiscordNotificationInterface
{
    /**
     * Gibt die Gültigen Webhook URLs zurück
     *
     * @return array<string>
     */
    public function getWebhookUrls(): array;

    /**
     * Gibt dass Fertig gebaute Embed zurück
     *
     * @return array<array<string, mixed>>
     */
    public function getEmbed(): array;
}
