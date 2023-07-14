<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventTypeElementClickService extends BaseEventTypeService implements EventTypeServiceInterface
{

    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'positionX' => $resource->position_x,
            'positionY' => $resource->position_y,
            'elementClasses' => $resource->element_classes,
            'innerText' => $resource->inner_text,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        $elementClasses = $data->get('elementClasses');
        $innerText = $data->get('innerText');

        try {
            DB::beginTransaction();

            $elementClickEvent = Event\EventElementClick::create([
                'position_x' => $data->get('positionX'),
                'position_y' => $data->get('positionY'),
                'element_classes' => $elementClasses ? Str::limit($elementClasses, 250) : null,
                'inner_text' => $innerText ? Str::limit($innerText, 250) : null,
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventElementClick::class,
                'event_id' => $elementClickEvent->id,
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
            'positionX' => 'required|numeric|min:0|max:65535',
            'positionY' => 'required|numeric|min:0|max:65535',
            'elementClasses' => 'nullable|string',
            'innerText' => 'nullable|string',
        ]);
    }
}
