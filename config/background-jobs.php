<?php

use App\Jobs\GenerateCsvFileJob;
use App\Jobs\TestJob;

return [
    // Register allowed job classes and their permitted methods
    'allowed_jobs' => [
        TestJob::class => ['handle'],
        GenerateCsvFileJob::class => ['handle'],
        // Add your job classes and allowed methods here
    ],
    // Default priority for new jobs (lower = higher priority)
    'default_priority' => env('BG_JOBS_DEFAULT_PRIORITY', 5),
    // Number of retry attempts on failure
    'retry_attempts' => env('BG_JOBS_RETRY_ATTEMPTS', 3),

    // Delay (in seconds) between retry attempts
    'retry_delay' => env('BG_JOBS_RETRY_DELAY', 5),

    'table' => env('BG_JOBS_TABLE', 'background_jobs'),
];

