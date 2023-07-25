<?php

namespace app\Enum\User;

enum UserCurrentPositionEnum: string
{
    case DEVELOPER = 'developer';
    case QA = 'qa';
    case PROJECT_MANAGER = 'project-manager';
    case MANAGER = 'manager';
    case OTHER = 'other';
}
