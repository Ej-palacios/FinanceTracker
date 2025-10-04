<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_type', // 'exchange', 'transaction', etc.
        'related_id',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Tipos de notificaciones
    const TYPE_EXCHANGE_REQUEST = 'exchange_request';
    const TYPE_EXCHANGE_APPROVED = 'exchange_approved';
    const TYPE_EXCHANGE_REJECTED = 'exchange_rejected';
    const TYPE_EXCHANGE_COMPLETED = 'exchange_completed';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function related()
    {
        return $this->morphTo();
    }

    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function getUrl()
    {
        switch ($this->related_type) {
            case 'exchange':
                return route('exchanges.show', $this->related_id);
            default:
                return '#';
        }
    }
}