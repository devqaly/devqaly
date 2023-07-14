<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventTypeElementScrollService extends BaseEventTypeService implements EventTypeServiceInterface
{

    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'scrollHeight' => $resource->scroll_height,
            'scrollWidth' => $resource->scroll_width,
            'scrollLeft' => $resource->scroll_left,
            'scrollTop' => $resource->scroll_top,
        ];
    }

    public function create(Collection $data, Session $session): void
    {

        try {
            DB::beginTransaction();

            $eventScroll = Event\EventElementScroll::create([
                'scroll_height' => $data->get('scrollHeight'),
                'scroll_width' => $data->get('scrollWidth'),
                'scroll_left' => $data->get('scrollLeft'),
                'scroll_top' => $data->get('scrollTop'),
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventElementScroll::class,
                'event_id' => $eventScroll->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventTypeElementClick event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'scrollHeight' => 'required|min:0|max:65535',
            'scrollWidth' => 'required|min:0|max:65535',
            'scrollLeft' => 'required|min:0|max:65535',
            'scrollTop' => 'required|min:0|max:65535',
        ]);
    }
}
