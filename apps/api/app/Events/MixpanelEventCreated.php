<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MixpanelEventCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $eventName,
        public array $properties,
        public ?string $email,
    ){}
}
