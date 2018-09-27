<?php

namespace App\Notifications;

use App\Mail\AutoResponderMail;
use App\Models\AutoResponder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BaseAutoResponderNotification extends Notification
{
    use Queueable;

    /**
     * @var null
     */
    protected $autoResponderSlug = null;

    /**
     * @var AutoResponder
     */
    protected $autoResponder;

    /**
     * Create a new notification instance.
     *
     * @param AutoResponder $autoResponder
     */
    public function __construct(AutoResponder $autoResponder)
    {
        $this->autoResponder = $autoResponder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return AutoResponderMail
     */
    public function toMail($notifiable)
    {
        $responder = $this->autoResponder->where('slug', $this->autoResponderSlug)->firstOrFail();

        return new AutoResponderMail($responder);
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
}
