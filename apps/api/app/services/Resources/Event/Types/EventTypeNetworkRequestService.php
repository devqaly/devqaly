<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class EventTypeNetworkRequestService extends BaseEventTypeService implements EventTypeServiceInterface
{
    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'method' => $resource->method,
            'url' => $resource->url,
            'requestHeaders' => $resource->request_headers ? json_decode($resource->request_headers) : null,
            'requestBody' => $resource->request_body ? (string) $resource->request_body : null,
            'responseHeaders' => $resource->response_headers ? json_decode($resource->response_headers) : null,
            'responseBody' => $resource->response_body ? (string) $resource->response_body : null,
            'responseStatus' => $resource->response_status !== null ? (string) $resource->response_status : null,
            'requestId' => $resource->request_id,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $eventNetwork = EventNetworkRequest::create([
                'method' => $data->get('method'),
                'url' => $data->get('url'),
                'response_status' => $data->get('responseStatus'),
                'request_headers' => Str::limit($data->get('requestHeaders'), 65530),
                'request_body' => Str::limit($data->get('requestBody'), 65530),
                'response_headers' => Str::limit($data->get('responseHeaders'), 65530),
                'response_body' => Str::limit($data->get('responseBody'), 65530),
                'request_id' => $data->get('requestId'),
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => EventNetworkRequest::class,
                'event_id' => $eventNetwork->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventTypeNetworkRequestService event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'method' => 'required|string|max:20|min:2',
            'url' => 'required|string|max:10000|min:2',
            'requestHeaders' => 'nullable|json|min:2',
            'requestBody' => 'nullable|string|min:2',
            'responseHeaders' => 'nullable|json|min:2',
            'responseBody' => 'nullable|string|min:2',
            'responseStatus' => 'nullable|numeric|min:100|max:599',
            'requestId' => 'required|string|uuid'
        ]);
    }
}
