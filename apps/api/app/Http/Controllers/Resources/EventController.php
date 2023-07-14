<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Event\GetEventsByRequestIdRequest;
use App\Http\Resources\Resources\EventResource;
use App\Models\Session\Event;
use App\services\Resources\Event\SessionEventService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get events that were initiated by a request.
     * E.g. a database transaction can only happen if a request have been made to the webserver.
     * E.g. a log can only happen if a request have been made to the server
     */
    public function getDatabaseTransactionsByRequestId(
        GetEventsByRequestIdRequest $request,
        Event\EventNetworkRequest   $networkRequestEvent,
        SessionEventService         $service
    ): AnonymousResourceCollection
    {
        $events = $service->getDatabaseTransactionsByRequest(
            collect($request->validated()),
            $networkRequestEvent,
        );

        $newCollection = $events->getCollection()->map(function (Event\EventDatabaseTransaction $databaseTransaction) {
            $event = new Event($databaseTransaction->event->toArray());

            $event->id = $databaseTransaction->event->id;
            $event->updated_at = $databaseTransaction->event->updated_at;
            $event->created_at = $databaseTransaction->event->created_at;

            $event->setRelation('eventable', $databaseTransaction);

            return $event;
        });

        $events->setCollection($newCollection);

        return EventResource::collection($events);
    }

    /**
     * Get events that were initiated by a request.
     * E.g. a database transaction can only happen if a request have been made to the webserver.
     * E.g. a log can only happen if a request have been made to the server
     */
    public function getLogsByRequestId(
        GetEventsByRequestIdRequest $request,
        Event\EventNetworkRequest $networkRequestEvent,
        SessionEventService         $service
    ): AnonymousResourceCollection
    {
        $events = $service->getLogsByRequest(
            collect($request->validated()),
            $networkRequestEvent,
        );

        $newCollection = $events->getCollection()->map(function (Event\EventLog $logEvent) {
            $event = new Event($logEvent->event->toArray());

            $event->id = $logEvent->event->id;
            $event->updated_at = $logEvent->event->updated_at;
            $event->created_at = $logEvent->event->created_at;

            $event->setRelation('eventable', $logEvent);

            return $event;
        });

        $events->setCollection($newCollection);

        return EventResource::collection($events);
    }
}
