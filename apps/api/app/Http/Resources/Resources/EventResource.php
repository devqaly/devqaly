<?php

namespace App\Http\Resources\Resources;

use App\services\Resources\Event\Types\EventTypeFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var EventTypeFactory $factory */
        $factory = app()->make(EventTypeFactory::class);

        $service = $factory->getEventTypeService($this->event_type);

        return collect([
            'id' => $this->id,
            'type' => $this->event_type,
            'source' => $this->source,
            'createdAt' => $this->created_at,
            'clientUtcEventCreatedAt' => $this->client_utc_event_created_at,
        ])
            ->when($this->resource->relationLoaded('eventable'), function (Collection $collection) use ($service) {
                $collection->put('event', $service->toArrayResource($this->eventable));
            })
            ->toArray();

    }
}
