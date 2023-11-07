<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event\EventCustomEvent;
use App\Models\Session\Event\EventDatabaseTransaction;
use App\Models\Session\Event\EventElementClick;
use App\Models\Session\Event\EventElementScroll;
use App\Models\Session\Event\EventLog;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Event\EventResizeScreen;
use App\Models\Session\Event\EventUrlChanged;

class EventTypeFactory
{
    public function getEventTypeService(string $type): EventTypeServiceInterface
    {
        return match ($type) {
            EventNetworkRequest::class => new EventTypeNetworkRequestService(),
            EventUrlChanged::class => new EventTypeUrlChangedService(),
            EventElementClick::class => new EventTypeElementClickService(),
            EventElementScroll::class => new EventTypeElementScrollService(),
            EventResizeScreen::class => new EventTypeResizeScreenService(),
            EventDatabaseTransaction::class => new EventTypeDatabaseTransactionService(),
            EventLog::class => new EventTypeLogService(),
            EventCustomEvent::class => new EventTypeCustomEventService(),
            default => throw new \Exception('This event type was not implemented yet: ' . $type),
        };
    }
}
