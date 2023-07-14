<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventTypeDatabaseTransactionService extends BaseEventTypeService implements EventTypeServiceInterface
{
    public function toArrayResource($resource): array
    {
        return [
            'id' => $resource->id,
            'sql' => $resource->sql,
            'executionTimeInMilliseconds' => $resource->execution_time_in_milliseconds,
            'requestId' => $resource->request_id,
        ];
    }

    public function create(Collection $data, Session $session): void
    {
        try {
            DB::beginTransaction();

            $databaseTransactionEvent = Event\EventDatabaseTransaction::create([
                'sql' => Str::limit($data->get('sql'), 65530),
                'execution_time_in_milliseconds' => $data->get('executionTimeInMilliseconds'),
                'request_id' => $data->get('requestId'),
            ]);

            Event::create([
                'source' => $data->get('source'),
                'session_id' => $session->id,
                'event_type' => Event\EventDatabaseTransaction::class,
                'event_id' => $databaseTransactionEvent->id,
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
            'sql' => 'required|string',
            'executionTimeInMilliseconds' => 'numeric|between:0,999999.99',
            'requestId' => 'uuid|nullable'
        ]);
    }
}
