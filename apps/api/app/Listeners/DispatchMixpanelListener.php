<?php

namespace App\Listeners;

use App\Events\MixpanelEventCreated;
use App\services\MixpanelService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class DispatchMixpanelListener implements ShouldQueue
{
    private MixpanelService $mixpanel;

    public $queue = 'mixpanel-events';

    public function __construct(MixpanelService $mixpanel)
    {
        $this->mixpanel = $mixpanel;
    }

    public function handle(MixpanelEventCreated $event): void
    {
        if (!$this->shouldProcessEvents()) return;

        $email = $event->email;

        if ($email) {
            $this->mixpanel->identify($email);
        }

        $this->mixpanel->track($event->eventName, $event->properties);
    }

    private function shouldProcessEvents(): bool
    {
        return config('services.mixpanel.host') !== null;
    }
}
