<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Event\EventCustomEvent;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTypeCustomEventService extends BaseEventTypeService implements EventTypeServiceInterface
{

    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'name' => $resource->name,
            'payload' => $resource->payload,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $customEvent = EventCustomEvent::create([
                    'payload' => $data->get('payload'),
                    'name' => $data->get('name'),
                ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventCustomEvent::class,
                'event_id' => $customEvent->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventDatabaseTransaction event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'payload' => 'json',
            'name' => 'required|string|max:255|min:1',
        ]);
    }
}
