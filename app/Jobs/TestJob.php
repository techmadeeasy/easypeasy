<?php

namespace App\Jobs;

class TestJob
{
    public function handle(string $test)
    {
        // Simulate some work
        echo $test;
        sleep(2);
        // Log the job execution
        \Log::info('TestJob executed successfully.');
    }
}
