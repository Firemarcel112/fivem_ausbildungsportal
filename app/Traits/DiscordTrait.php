<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait DiscordTrait
{
    /**
     * Summary of setEmbed
     *
     * @param  string                                           $title
     * @param  string                                           $description
     * @param  string                                           $url
     * @param  int                                              $color
     * @param  array<string, int|string> $fields
     * @param  string                                           $footerText
     * @param  string                                           $footerIconUrl
     * @param  string                                           $thumbnailUrl
     * @param  string                                           $imageUrl
     * @param  string                                           $content
     * @return array<mixed>
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
}
