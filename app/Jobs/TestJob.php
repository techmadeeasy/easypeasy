<?php

namespace App\Jobs;

class TestJob
{
    public function handle(string $test)
    {
        sleep(5);
        // Log the job execution
        \Log::info('TestJob executed successfully.');

        return $this;
    }
}
