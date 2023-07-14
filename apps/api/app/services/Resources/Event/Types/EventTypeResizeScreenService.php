<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Event\EventNetworkRequest;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTypeResizeScreenService extends BaseEventTypeService implements EventTypeServiceInterface
{

    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'innerHeight' => $resource->inner_height,
            'innerWidth' => $resource->inner_width,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $resizeEvent = Event\EventResizeScreen::create([
                'inner_height' => $data->get('innerHeight'),
                'inner_width' => $data->get('innerWidth'),
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventResizeScreen::class,
                'event_id' => $resizeEvent->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventTypeResizeScreenService event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'innerHeight' => 'required|numeric|min:0|max:65535',
            'innerWidth' => 'required|numeric|min:0|max:65535'
        ]);
    }
}
