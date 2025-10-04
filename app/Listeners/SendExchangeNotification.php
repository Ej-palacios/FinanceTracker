<?php

namespace App\Listeners;

use App\Events\ExchangeRequestCreated;
use App\Services\NotificationService;
class SendExchangeNotification
{

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ExchangeRequestCreated $event): void
    {
        // Notificar al usuario que recibe la solicitud (to_user)
        NotificationService::sendExchangeRequest(
            $event->toUser->id,
            $event->fromUser,
            $event->exchange
        );
    }
}
