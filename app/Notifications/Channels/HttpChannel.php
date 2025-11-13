<?php

namespace App\Notifications\Channels;

use Illuminate\Http\Client\RequestException;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class HttpChannel
{
    /**
     * Send the given notification.
     *
     * @throws RequestException
     */
    public function send(object $notifiable, Notification $notification): void
    {
        /**
         * @var array $message
         */
        $message = $notification->toHttp($notifiable);

        /**
         * @var string $token
         */
        $token = $notifiable->routeNotificationFor('http', $notification);

        Http::withToken($token)
            ->post(url: 'https://puklipo.com/api/status', data: $message)
            ->dontTruncateExceptions()
            ->throw();
    }
}
