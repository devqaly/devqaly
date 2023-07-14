<?php

namespace App\services\Resources\Event;

use App\Models\Session\Event\EventDatabaseTransaction;
use App\Models\Session\Event\EventLog;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Session;
use App\Traits\UsesPaginate;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SessionEventService
{
    use UsesPaginate;

    public function getSessionEvents(Collection $filters, Session $session): LengthAwarePaginator
    {
        $events = $session->events()->with('eventable');

        if ($filters->has('startCreatedAt')) {
            $events->where('created_at', '>', $filters->get('startCreatedAt'));
        }

        if ($filters->has('endCreatedAt')) {
            $events->where('created_at', '<', $filters->get('endCreatedAt'));
        }

        if ($filters->has('createdAtOrder')) {
            $events->orderBy('created_at', $filters->get('createdAtOrder'));
        } else {
            $events->orderByDesc('created_at');
        }

        return $events->paginate(100);
    }

    public function getDatabaseTransactionsByRequest(Collection $filters, EventNetworkRequest $eventNetworkRequest): LengthAwarePaginator
    {
        $databaseEvents = EventDatabaseTransaction::with('event')
            ->where('request_id', $eventNetworkRequest->request_id);

        return $databaseEvents->paginate($this->getPerPage());
    }

    public function getLogsByRequest(Collection $filters, EventNetworkRequest $eventNetworkRequest): LengthAwarePaginator
    {
        $logsEvents = EventLog::with('event')
            ->where('request_id', $eventNetworkRequest->request_id);

        return $logsEvents->paginate($this->getPerPage());
    }
}
