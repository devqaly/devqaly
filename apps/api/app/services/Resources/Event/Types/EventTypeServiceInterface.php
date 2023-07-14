<?php

namespace App\services\Resources\Event\Types;

use App\Models\Session\Event;
use App\Models\Session\Session;
use Illuminate\Support\Collection;

interface EventTypeServiceInterface
{
    public function toArrayResource($resource): array;
    public function create(Collection $data, Session $session): void;
    public function createValidationRules(): array;
}
