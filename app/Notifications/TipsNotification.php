<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\HttpChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Revolution\Laravel\Notification\DiscordWebhook\DiscordChannel;
use Revolution\Laravel\Notification\DiscordWebhook\DiscordMessage;
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
            NostrChannel::class,
            DiscordChannel::class,
            HttpChannel::class,
        ];
    }

    public function toDiscordWebhook(object $notifiable): DiscordMessage
    {
        return DiscordMessage::create(content: Str::truncate($this->tips, 1800));
    }

    public function toNostr(object $notifiable): NostrMessage
    {
        return NostrMessage::create(
            content: $this->tips.PHP_EOL.'#laravel',
            tags: [HashTag::make(t: 'laravel')],
        );
    }

    public function toHttp(object $notifiable): array
    {
        return [
            'content' => $this->tips,
        ];
    }
}
