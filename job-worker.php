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

[$class, $method, $paramsString, $delay, $priority] = array_pad($argv, 5, '');
$params = $paramsString === '' ? [] : explode(',', $paramsString);

runBackgroundJob(
    $class,
    $method,
    $params,
    (int) $delay,
    (int) $priority
);

