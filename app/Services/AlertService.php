<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class AlertService
{
    /**
     * Fügt einen neuen Alert hinzu.
     *
     * @param string $message
     * @param string $type
     * @param bool $static
     * @return void
     */
    public function addAlert(string $message, string $type = 'info', bool $static = false)
    {
        $alerts = Session::get('alerts', []);

        // Neuen Alert hinzufügen
        $alerts[] = [
            'message' => $message,
            'type' => $type,
            'static' => $static,
        ];

        // Speichern der Alerts in der Session
        Session::put('alerts', $alerts);
    }

    /**
     * Gibt alle gespeicherten Alerts zurück.
     *
     * @return array
     */
    public function getAlerts()
    {
        return Session::get('alerts', []);
    }

    /**
     * Löscht alle gespeicherten Alerts.
     *
     * @return void
     */
    public function clearAlerts()
    {
        Session::forget('alerts');
    }

    /**
     * Löscht die Alerts eines bestimmten Typs.
     *
     * @param string $type
     * @return void
     */
    public function clearAlertsByType(string $type)
    {
        $alerts = array_filter(Session::get('alerts', []), function ($alert) use ($type) {
            return $alert['type'] !== $type;
        });

        Session::put('alerts', $alerts);
    }
}
