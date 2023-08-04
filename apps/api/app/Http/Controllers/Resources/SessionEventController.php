<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Session\Event\CreateSessionEventRequest;
use App\Http\Requests\Resources\Session\Event\IndexSessionEventsRequest;
use App\Http\Resources\Resources\EventResource;
use App\Models\Session\Session;
use App\services\Resources\Event\SessionEventService;
use App\services\Resources\Event\Types\EventTypeFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class SessionEventController extends Controller
{
    public function index(
        IndexSessionEventsRequest $request,
        SessionEventService $service,
        Session $projectSession
    ): AnonymousResourceCollection
    {
        $events = $service->getSessionEvents(collect($request->validated()), $projectSession);

        return EventResource::collection($events);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateSessionEventRequest $request, Session $projectSession): JsonResponse
    {
        /** @var EventTypeFactory $factory */
        $factory = app()->make(EventTypeFactory::class);

        $service = $factory->getEventTypeService($request->get('type'));

        $service->create(collect($request->validated()), $projectSession);

        return response()->json(null, Response::HTTP_NO_CONTENT);
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
}
