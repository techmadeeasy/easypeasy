<?php

namespace App;

enum JobStatusEnum: string
{
    case RUNNING = 'running';
    case SUCCESS = 'success';
    case FAILURE = 'failure';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
