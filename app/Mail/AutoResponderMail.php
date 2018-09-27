<?php

namespace App\Mail;

use App\Models\AutoResponder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoResponderMail extends Mailable
{
    use Queueable, SerializesModels;

    const VIEW = 'emails.auto-responders.general';

    /**
     * @var AutoResponder
     */
    private $autoResponder;

    /**
     * Create a new message instance.
     *
     * @param AutoResponder $autoResponder
     */
    public function __construct(AutoResponder $autoResponder)
    {
        $this->autoResponder = $autoResponder;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(static::VIEW)->with([
            'content' => $this->autoResponder->content
        ]);
    }
}
