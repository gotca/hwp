<?php

namespace App\Notifications;

use App\Channels\LogChannel;
use App\Models\Rank;
use App\Notifications\Traits\Loggable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NotificationChannels\Twitter\TwitterChannel;
use NotificationChannels\Twitter\TwitterStatusUpdate;

class RankingsUpdated extends Notification implements ShouldQueue
{
    use Loggable, Queueable;

    /**
     * @var Rank
     */
    protected $rank;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Rank $rank)
    {
        $this->rank = $rank;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->sendToLog() ? [LogChannel::class] : [TwitterChannel::class];
    }

    /**
     * Get the log representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toLog($notifiable)
    {
        return [
            'message' => $this->getMessage(),
            'context' => $this->rank->toArray()
        ];
    }

    /**
     * Get the twitter status update for this notification.
     *
     * @param $notifiable
     * @return TwitterStatusUpdate
     */
    public function toTwitter($notifiable)
    {
        return new TwitterStatusUpdate($this->getMessage());
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    protected function getMessage() {
        if ($this->rank->tied) {
            $key = 'notifications.rankingsTied';
        } else {
            $key = 'notifications.rankings';
        }

        return trans($key, ['rank' => $this->rank->rank]);
    }
}
