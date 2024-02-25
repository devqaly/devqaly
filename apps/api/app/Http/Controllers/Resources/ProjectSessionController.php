<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Project\Session\CreateSessionRequest;
use App\Http\Requests\Resources\Project\Session\IndexSessionRequest;
use App\Http\Resources\Resources\SessionResource;
use App\Models\Project\Project;
use App\services\Resources\SessionService;
use App\services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectSessionController extends Controller
{
    public function index(
        IndexSessionRequest $request,
        Project             $project,
        SessionService      $service
    ): AnonymousResourceCollection
    {
        $sessions = $service->listSessions(
            collect($request->validated()),
            $request->user(),
            $project
        );

        return SessionResource::collection($sessions);
    }

    public function store(
        CreateSessionRequest $request,
        Project $project,
        SessionService $sessionService,
        SubscriptionService $subscriptionService
    ): SessionResource
    {
        $session = $sessionService->createSession(
            collect($request->validated()),
            $request->user(),
            $project
        );

        $sessionLengthInSeconds = 600;

        if (!config('devqaly.isSelfHosting')) {
            $sessionLengthInSeconds = $subscriptionService->getMaximumSessionLength($session->project->company);
        }

        return (new SessionResource($session))
            ->additional([
                'data' => [
                    'secretToken' => $session->secret_token
                ],
                'meta' => [
                    'maximumSessionLengthInSeconds' => $sessionLengthInSeconds
                ]
            ]);
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
