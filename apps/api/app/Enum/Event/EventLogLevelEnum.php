<?php

namespace App\Enum\Event;

enum EventLogLevelEnum: string
{
    case EMERGENCY = 'emergency';
    case ALERT = 'alert';
    case CRITICAL = 'critical';
    case ERROR = 'error';
    case WARNING = 'warning';
    case NOTICE = 'notice';
    case INFORMATIONAL = 'informational';
    case DEBUG = 'debug';
}
