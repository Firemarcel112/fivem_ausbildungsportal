<?php

namespace App\Traits;

use App\Models\Fractions\Fraction;
use GuzzleHttp\Client;

trait DiscordTrait
{
    /**
     * Summary of setEmbed
     *
     * @param  string $title
     * @param  string $description
     * @param  string $url
     * @param  int    $color
     * @param  array  $fields        (name, value, inline (bool))
     * @param  string $footerText
     * @param  string $footerIconUrl
     * @param  string $thumbnailUrl
     * @param  string $imageUrl
     * @param  string $content
     * @return array
     */
    public function buildEmbed(string $title, array $fields = [], string $description = '', string $footerText = 'Ausbildungsportal', string $footerIconUrl = '', string $url = '', int $color = 3447003, string $thumbnailUrl = '', string $imageUrl = '', string $content = ''): array
    {
        $embedData = [
            'embeds' => [
                [
                    'title' => $title,
                    'color' => $color,
                    'fields' => $fields,
                    'footer' => [
                        'text' => $footerText,
                    ],
                    'timestamp' => now()->toIso8601String(),
                ],
            ],
        ];
        if (!empty($content)) {
            $embedData['content'] = $content;
        }

        if (!empty($description)) {
            $embedData['embeds'][0]['description'] = $description;
        }

        if (!empty($url)) {
            $embedData['embeds'][0]['url'] = $url;
        }

        if (!empty($thumbnailUrl)) {
            $embedData['embeds'][0]['thumbnail'] = [
                'url' => $thumbnailUrl,
            ];
        }

        if (!empty($imageUrl)) {
            $embedData['embeds'][0]['image'] = [
                'url' => $imageUrl,
            ];
        }
        if (!empty($footerIconUrl)) {
            $embedData['embeds'][0]['footer']['icon_url'] = $footerIconUrl;
        }

        return $embedData;
    }

    /**
     * Send the embed message to a Discord webhook
     *
     * @param  string $webhookUrl
     * @param  array  $embedData
     * @return void
     */
    public function sendToDiscord(string $webhookUrl, array $embedData): void
    {
        try {
            $client = new Client;
            $response = $client->post($webhookUrl, [
                'json' => $embedData,
                'verify' => false,
            ]);

            if ($response->getStatusCode() !== 204) {
                throw new \Exception('Failed to send message to Discord');
            }
        } catch (\Exception $e) {
            \Log::error('Fehler beim verseden von ' . $webhookUrl, (array) $e);
            throw new \Exception('Fehler beim versenden von ' . $webhookUrl);
        }
    }

    /**
     * Gibt die Webhook_urls von den Fraktionen zurück
     *
     * @param  array $notify
     * @return array
     */
    public function getFractionsWebhookUrls(array $notify = [])
    {
        $webhook_urls = [];
        if (!empty($notify)) {
            if (in_array('alle', $notify)) {
                $webhook_urls = Fraction::pluck('discord_webhook')
                    ->toArray();
            } else {
                $webhook_urls = Fraction::whereIn('fraktion_id', $notify)
                    ->pluck('discord_webhook')
                    ->toArray();
            }
        }

        return $webhook_urls;
    }

    public function makeDiscordRequest(string $url, array $data = [], string $method = 'GET')
    {
        $client = new Client;

        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }

        return $client->request($method, env('DISCORD_API_URL') . $url, $data);
    }
}
