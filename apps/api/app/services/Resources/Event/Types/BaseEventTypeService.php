<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event\EventDatabaseTransaction;

class BaseEventTypeService
{
    protected function baseValidationRules(array $validations): array
    {
        return [
            ...$validations,
            'source' => 'required|string|min:1|max:255',
            'clientUtcEventCreatedAt' => 'required|date:Y-m-d\TH:i:s.SSSSSS\Z'
        ];
    }

    static function needsSecretTokenValidation(string $eventType): bool
    {
        return $eventType === EventDatabaseTransaction::class;
    }
}
