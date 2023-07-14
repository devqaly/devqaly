<?php

namespace App\Enum\Sessions;

enum SessionVideoStatusEnum: string
{
    case IN_QUEUE_FOR_CONVERSION = 'in-queue-for-conversion';
    case CONVERTING = 'converting';
    case CONVERTED = 'converted';
}
