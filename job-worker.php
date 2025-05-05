<?php

use Illuminate\Support\Arr;

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
$allowedJobs = Arr::get($config, 'allowed_jobs', []);
$mappedJobs = array_map('class_basename', array_keys($allowedJobs));
$jobIndex = array_search($class, $mappedJobs, true);

if ($jobIndex === false) {
    $classIsAllowed = false;
} else {
    $fullyQualifiedClass = array_keys($allowedJobs)[$jobIndex];
    $classIsAllowed = true;
}

// Security: only allow configured jobs
if (!$classIsAllowed) {
    error_log("[" . now() . "] Validation error: Class not allowed: {$class}\n", 3, storage_path('logs/background_jobs_errors.log'));
    echo "Error: The class '{$class}' is not allowed.\n";
    exit(1);
}

if (!in_array($method, $allowedJobs[$fullyQualifiedClass] ?? [], true)) {
    error_log("[" . now() . "] Validation error: Method not allowed or does not exist: {$class}@{$method}\n", 3, storage_path('logs/background_jobs_errors.log'));
    echo "Error: The method '{$method}' is not allowed or does not exist in the class '{$class}'.\n";
    exit(1);
}

$attempts = $config['retry_attempts'];
$delay = $config['retry_delay'];

for ($i = 1; $i <= $attempts; $i++) {
    try {
        info("Running job: {$class}@{$method} attempt {$i}");
        $instance = new $class();
        call_user_func_array([$instance, $method], $params);
        info("Completed job: {$class}@{$method}");
        exit(0);
    } catch (Throwable $e) {
        error_log("[" . now() . "] Error on {$class}@{$method} attempt {$i}: " . $e->getMessage() . "\n", 3, storage_path('logs/background_jobs_errors.log'));
        if ($i < $attempts) {
            sleep($delay);
        } else {
            error_log("[" . now() . "] Job failed after {$attempts} attempts: {$class}@{$method}\n", 3, storage_path('logs/background_jobs_errors.log'));
            exit(1);
        }
    }
}

