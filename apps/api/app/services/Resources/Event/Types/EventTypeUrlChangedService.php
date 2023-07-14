<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Session;
use Faker\Provider\Base;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTypeUrlChangedService extends BaseEventTypeService implements EventTypeServiceInterface
{

    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'toUrl' => $resource->to_url,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $urlChangedEvent = Event\EventUrlChanged::create([
                'to_url' => $data->get('toUrl')
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventUrlChanged::class,
                'event_id' => $urlChangedEvent->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventTypeUrlChangedService event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'toUrl' => 'required|url|max:65535'
        ]);
    }
}
