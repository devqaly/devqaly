<?php

namespace App\Http\Controllers\Resources;

use App\Enum\S3NamespacesEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Session\CreateSessionRequest;
use App\Http\Requests\Resources\Session\StoreSessionVideoRequest;
use App\Http\Resources\Resources\SessionResource;
use App\Jobs\Session\ProcessSessionVideo;
use App\Models\Session\Session;
use App\services\Resources\SessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\NoReturn;

class SessionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(CreateSessionRequest $request, SessionService $service)
    {

    }

    public function show(Session $session, Request $request): SessionResource
    {
        $this->authorize('view', $session);

        $session->load('createdBy');

        return new SessionResource($session);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function assignSessionToLoggedUser(Request $request, Session $session): SessionResource
    {
        $this->validate($request, [
            'userId' => 'required|exists:users,id'
        ]);

        $this->authorize('assignSessionToUser', [$session, $request->user()]);

        $session->update(['created_by_id' => $request->get('userId')]);

        $session->load('project');

        return new SessionResource($session);
    }

    public function storeVideo(StoreSessionVideoRequest $request, Session $projectSession): JsonResponse
    {
        $extension = $request->video->extension();

        $request->video->storeAs(S3NamespacesEnum::VIDEOS_TO_CONVERT->value, "$projectSession->id.$extension");

        $projectSession->video_extension = $extension;
        $projectSession->save();

        ProcessSessionVideo::dispatch($projectSession);

        return response()->json(['data' => ['message' => 'Video uploaded successfully!']]);
    }

    #[NoReturn] public function streamVideo(Session $session): void
    {
        $stream = new \App\services\VideoStream(storage_path("app/videos/{$session->id}.webm"));

        $stream->start();

        exit();
    }
}
