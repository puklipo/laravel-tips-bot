<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Notifications\Channels\HttpChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Revolution\Laravel\Notification\DiscordWebhook\DiscordChannel;
use Revolution\Laravel\Notification\DiscordWebhook\DiscordEmbed;
use Revolution\Laravel\Notification\DiscordWebhook\DiscordMessage;
use Revolution\Nostr\Notifications\NostrChannel;
use Revolution\Nostr\Notifications\NostrMessage;
use Revolution\Nostr\Tags\HashTag;

class ReleaseNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $repo,
        protected string $ver,
        protected string $url,
        protected string $note,
    ) {
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
        return DiscordMessage::create()
            ->embed(DiscordEmbed::make(
                title: $this->repo.' '.$this->ver,
                description: Str::truncate($this->note, 1800),
                url: $this->url,
            ));
    }

    public function toNostr(object $notifiable): NostrMessage
    {
        $content = collect([
            $this->repo.' '.$this->ver,
            $this->url,
            '',
            $this->note,
            '#laravel',
        ])->join(PHP_EOL);

        return NostrMessage::create(
            content: $content,
            tags: [HashTag::make(t: 'laravel')],
        );
    }

    public function toHttp(object $notifiable): array
    {
        $content = collect([
            $this->url,
            '',
            $this->note,
        ])->join(PHP_EOL);

        return [
            'content' => $content,
            'title' => $this->repo.' '.$this->ver,
        ];
    }
}
