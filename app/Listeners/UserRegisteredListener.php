<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Models\AutoResponder;
use App\Notifications\PaidWelcome;
use App\Notifications\TrialWelcome;

class UserRegisteredListener
{
    /**
     * UserRegisteredListener constructor.
     */
    public function __construct()
    {

    }

    /**
     * @param UserRegistered $event
     */
    public function handle(UserRegistered $event)
    {
        $user = $event->getUser();

        if ($user->isOnTrial()) {
            $user->notify(new TrialWelcome(new AutoResponder()));
        } else {
            $user->notify(new PaidWelcome(new AutoResponder()));
        }
    }
}
