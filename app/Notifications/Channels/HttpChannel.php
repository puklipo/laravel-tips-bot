<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class HttpChannel
{
    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toHttp($notifiable);
        $token = $notifiable->routeNotificationFor('http', $notification);

        $response = Http::withToken($token)
            ->post('https://puklipo.com/api/status', $message);

        info($response->body());
    }
}
