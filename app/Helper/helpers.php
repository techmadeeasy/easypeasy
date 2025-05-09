<?php

use App\Models\BackgroundJob;
use Carbon\Carbon;
use Symfony\Component\Process\Process;
use App\JobStatusEnum;

if (! function_exists('runBackgroundJob')) {
    /**
     * Trigger a background job execution with updated class validation.
     *
     * @param string $class  Short or fully-qualified class name
     * @param string $method Method to call on the class
     * @param array  $params Parameters to pass to the method
     * @param int    $delay  Delay in seconds before the job is available
     * @param int    $priority Job priority (lower number = higher priority)
     * @throws InvalidArgumentException if the job or method is not allowed.
     */
    function runBackgroundJob(string $class, string $method, array $params = [], int $delay = 0, int $priority = 5): bool
    {
        $config = config('background-jobs');
        $allowedJobs = $config['allowed_jobs'] ?? [];

        if (isset($allowedJobs[$class])) {
            $fqcn = $class;
        } else {
            $mappedJobs = array_map('class_basename', array_keys($allowedJobs));
            $jobIndex = array_search($class, $mappedJobs, true);

            if ($jobIndex === false) {
                throw new InvalidArgumentException("Job not allowed: {$class}@{$method}");
            }
            $fqcn = array_keys($allowedJobs)[$jobIndex];
        }

        if (!in_array($method, $allowedJobs[$fqcn] ?? [], true)) {
            error_log("Job not allowed: {$class}@{$method}" .  PHP_EOL, 3, storage_path('logs/background_jobs_errors.log'));
            throw new InvalidArgumentException("Job not allowed: {$class}@{$method}");
        }
        $availableAt = Carbon::now()->addSeconds($delay);

        BackgroundJob::create([
            'job_class'    => $fqcn,
            'job_method'   => $method,
            'payload'      => json_encode($params),
            'priority'     => $priority ?? config('background-jobs.default_priority'),
            'available_at' => $availableAt,
            'max_attempts' => config('background-jobs.retry_attempts'),
            'status'       => JobStatusEnum::QUEUEUED->value,
        ]);

        return true;
    }
}
