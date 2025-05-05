<?php

use Symfony\Component\Process\Process;

if (! function_exists('runBackgroundJob')) {
    /**
     * Trigger a background job execution
     *
     * @param string $class  Fully-qualified class name
     * @param string $method Method to call on the class
     * @param array  $params Parameters to pass to the method
     */
    function runBackgroundJob(string $class, string $method, array $params = []): void
    {
        $config = config('background-jobs');
        if (!isset($config['allowed_jobs'][$class]) || !in_array($method, $config['allowed_jobs'][$class], true)) {
            throw new InvalidArgumentException("Job not allowed: {$class}@{$method}");
        }

        $paramString = implode(',', array_map('escapeshellarg', $params));
        $binary = PHP_BINARY;
        $script = base_path('run-job.php');

        $process = new Process([
            $binary,
            $script,
            $class,
            $method,
            $paramString,
        ]);
        $process->setTimeout(0);
        $process->start();
    }
}
