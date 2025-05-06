<?php

namespace App;

enum JobStatusEnum: string
{
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILURE = 'failure';
    case CANCELLED = 'cancelled';
    case QUEUEUED = 'queued';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
