<?php

namespace App\Notifications;

use App\Models\AutoResponder;
use Illuminate\Bus\Queueable;

class TrialSevenDaysBeforePayment extends BaseAutoResponderNotification
{
    use Queueable;

    /**
     * @var string
     */
    protected $autoResponderSlug = '7_day_reminder_trial';

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
