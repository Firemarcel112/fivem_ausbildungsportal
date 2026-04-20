<?php

namespace App\Listeners\Discord;

use App\Interfaces\DiscordNotificationInterface;
use App\Traits\DiscordTrait;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\foundation\Queue\Queueable;

class SendDiscordEmbed implements ShouldQueue
{
    use DiscordTrait;
    use Queueable;

    public $tries = 3;

    public function handle(DiscordNotificationInterface $event)
    {

        $embed = $event->getEmbed();

        if (empty($embed)) {
            return;
        }

        foreach ($event->getWebhookUrls() as $url) {
            try {
                $this->sendToDiscord($url, $embed);
            } catch (Exception $e) {
                \Sentry\captureException(throw new Exception(
                    str('Discord Nachricht konnte nicht versendet werden: ')
                        ->append($e->getMessage())
                        ->append(get_class($event))
                        ->value()
                ));
            }
        }
    }
}
