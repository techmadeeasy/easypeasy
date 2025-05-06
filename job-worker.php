<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Parse CLI arguments
array_shift($argv); // remove script name
if (count($argv) < 2) {
    echo "Usage: php run-job.php ClassName methodName \"param1,param2\"\n";
    exit(1);
}

[$class, $method, $paramsString] = array_pad($argv, 3, '');
$params = $paramsString === '' ? [] : explode(',', $paramsString);

// Load config
$config = config('background-jobs');

// Validate job
if (!$validatedClass = validateJob($class, $method, $config)) {
    exit(1);
}

// Execute job with retries
executeJobWithRetries($validatedClass, $method, $params, $config);

/**
 * Log each job execution into the database.
 *
 * @param string $class
 * @param string $method
 * @param string $status
 * @param string|null $errorTrace
 * @param int $attempts
 * @return void
 */
function recordJobExecution(string $class, string $method, string $status, int $attempts, ?string $errorTrace = null): void
{
    DB::table('job_logs')->insert([
        'job_class'   => $class,
        'method'      => $method,
        'status'      => $status,
        'executed_at' => now(),
        'error_trace' => $errorTrace,
        'attempts'    => $attempts,
    ]);
}

/**
 * Validate if the job class and method are allowed.
 *
 * @param string $class
 * @param string $method
 * @param array $config
 * @return mixed
 */
function validateJob(string $class, string $method, array $config): mixed
{
    $allowedJobs = Arr::get($config, 'allowed_jobs', []);
    $mappedJobs = array_map('class_basename', array_keys($allowedJobs));
    $jobIndex = array_search($class, $mappedJobs, true);

    if ($jobIndex === false) {
        error_log("[" . now() . "] Validation error: Class not allowed: {$class}\n", 3, storage_path('logs/background_jobs_errors.log'));
        echo "Error: The class {$class} is not allowed.\n";
        return false;
    }

    $fullyQualifiedClass = array_keys($allowedJobs)[$jobIndex];

    if (!in_array($method, $allowedJobs[$fullyQualifiedClass] ?? [], true)) {
        error_log("[" . now() . "] Validation error: Method not allowed or does not exist: {$class}@{$method}\n", 3, storage_path('logs/background_jobs_errors.log'));
        echo "Error: The method {$method} is not allowed or does not exist in the class {$class}.\n";
        return false;
    }

    return $fullyQualifiedClass;
}

/**
 * Execute a job with retry logic.
 *
 * @param string $class
 * @param string $method
 * @param array $params
 * @param array $config
 * @return void
 */
function executeJobWithRetries(string $class, string $method, array $params, array $config): void
{
    $attempts = $config['retry_attempts'] ?? 3;
    $delay = $config['retry_delay'] ?? 5;

    for ($i = 1; $i <= $attempts; $i++) {
        try {
            info("Running job: {$class}@{$method} attempt {$i}");
            executeJob($class, $method, $params);
            info("Completed job: {$class}@{$method}");
            recordJobExecution($class, $method, 'success', $i, null);
            return;
        } catch (Throwable $e) {
            error_log("[" . now() . "] Error on {$class}@{$method} attempt {$i}: " . $e->getMessage() . "\n", 3, storage_path('logs/background_jobs_errors.log'));
            if ($i < $attempts) {
                sleep($delay);
            } else {
                error_log("[" . now() . "] Job failed after {$attempts} attempts: {$class}@{$method}\n", 3, storage_path('logs/background_jobs_errors.log'));
                recordJobExecution($class, $method, 'failure', $i, $e->getTraceAsString());
                exit(1);
            }
        }
    }
}

/**
 * Execute a single job.
 *
 * @param string $class
 * @param string $method
 * @param array $params
 * @return void
 */
function executeJob(string $class, string $method, array $params): void
{
    $instance = new $class();
    call_user_func_array([$instance, $method], $params);
}
