<?php

namespace App\Notifications;

use App\Models\AutoResponder;
use Illuminate\Bus\Queueable;

class CreditCardExpiration extends BaseAutoResponderNotification
{
    use Queueable;

    /**
     * @var string
     */
    protected $autoResponderSlug = 'credit_card_expiration';

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
