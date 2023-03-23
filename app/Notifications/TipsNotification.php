<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;
use Revolution\Nostr\Notifications\NostrChannel;
use Revolution\Nostr\Notifications\NostrMessage;
use Revolution\Nostr\Tags\HashTag;

class TipsNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected string $tips)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [
            //'mail',
            DiscordChannel::class,
            NostrChannel::class,
        ];
    }

    public function toDiscord(object $notifiable): DiscordMessage
    {
        return DiscordMessage::create($this->tips);
    }

    public function toNostr(object $notifiable): NostrMessage
    {
        return NostrMessage::create(
            content: $this->tips.PHP_EOL.'#laravel',
            tags: [HashTag::make(t: 'laravel')],
        );
    }
}
