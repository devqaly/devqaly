<?php

namespace App\services\Resources\Event\Types;

use App\Enum\Event\EventLogLevelEnum;
use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EventTypeLogService extends BaseEventTypeService implements EventTypeServiceInterface
{
    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'level' => $resource->level,
            'log' => $resource->log,
            'requestId' => $resource->request_id,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $eventLog = Event\EventLog::create([
                'log' => Str::limit($data->get('log'), 65530),
                'level' => $data->get('level'),
                'request_id' => $data->get('requestId'),
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventLog::class,
                'event_id' => $eventLog->id,
                'client_utc_event_created_at' => $data->get('clientUtcEventCreatedAt')
            ]);

            DB::commit();
        } catch (\Exception $e) {
            Log::error('There was an error creating EventLog event: ' . $e->getMessage());

            DB::rollBack();
        }
    }

    public function createValidationRules(): array
    {
        return $this->baseValidationRules([
            'level' => [
                'required',
                Rule::enum(EventLogLevelEnum::class)
            ],
            'log' => 'required|string',
            'requestId' => 'uuid|nullable'
        ]);
    }
}
