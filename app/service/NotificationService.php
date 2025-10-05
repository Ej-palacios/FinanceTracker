<?php

namespace App\service;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    public static function sendExchangeRequest($toUserId, $fromUser, $exchange)
    {
        return Notification::create([
            'user_id' => $toUserId,
            'type' => Notification::TYPE_EXCHANGE_REQUEST,
            'title' => 'Nueva Solicitud de Intercambio',
            'message' => "{$fromUser->name} te ha enviado una solicitud de intercambio de {$exchange->from_amount} {$exchange->from_currency}",
            'related_type' => 'exchange',
            'related_id' => $exchange->id,
            'is_read' => false
        ]);
    }

    public static function sendExchangeApproved($toUserId, $fromUser, $exchange)
    {
        return Notification::create([
            'user_id' => $toUserId,
            'type' => Notification::TYPE_EXCHANGE_APPROVED,
            'title' => 'Intercambio Aprobado',
            'message' => "{$fromUser->name} ha aprobado tu intercambio. Se han completado las transacciones.",
            'related_type' => 'exchange',
            'related_id' => $exchange->id,
            'is_read' => false
        ]);
    }

    // Más métodos para otros tipos de notificaciones...
}