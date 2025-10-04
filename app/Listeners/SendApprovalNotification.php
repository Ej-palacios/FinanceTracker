<?php

namespace App\Listeners;

use App\Events\ExchangeApproved;
use App\Services\NotificationService;
class SendApprovalNotification
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
    public function handle(ExchangeApproved $event): void
    {
        // Notificar al usuario que envió la solicitud (from_user) que fue aprobada
        NotificationService::sendExchangeApproved(
            $event->fromUser->id,
            $event->toUser,
            $event->exchange
        );
    }
}
