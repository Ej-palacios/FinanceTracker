<?php

namespace App\Events;

use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExchangeRequestCreated
{
    use Dispatchable, SerializesModels;

    public $exchange;
    public $fromUser;
    public $toUser;

    /**
     * Create a new event instance.
     */
    public function __construct(ExchangeRequest $exchange, User $fromUser, User $toUser)
    {
        $this->exchange = $exchange;
        $this->fromUser = $fromUser;
        $this->toUser = $toUser;
    }
}
