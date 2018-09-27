<?php

namespace App\Notifications;

use App\Models\AutoResponder;
use Illuminate\Bus\Queueable;

class MailingListUnsubscribe extends BaseAutoResponderNotification
{
    use Queueable;

    /**
     * @var string
     */
    protected $autoResponderSlug = 'unsubscribe';

    /**
     * Create a new notification instance.
     *
     * @param AutoResponder $autoResponder
     */
    public function __construct(AutoResponder $autoResponder)
    {
        parent::__construct($autoResponder);
    }
}
