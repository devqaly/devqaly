<?php

namespace App\Enum;

enum S3NamespacesEnum: string
{
    case SESSION_VIDEOS = 'videos';
    case VIDEOS_TO_CONVERT = 'videos-to-convert';
}
