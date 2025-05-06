<?php
// tests/JobWorkerTest.php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    // Save original argv.
    Artisan::call('migrate');
    $this->originalArgv = $GLOBALS['argv'] ?? [];
});

afterEach(function () {
    // Restore original argv.
    $GLOBALS['argv'] = $this->originalArgv;
});

/** @runInSeparateProcess */
test('Usage message is printed when insufficient arguments', function () {
    // Set argv with insufficient arguments.
    $GLOBALS['argv'] = ['job-worker.php', 'OnlyOneArg'];

    ob_start();

    // Define a dummy runBackgroundJob if it does not exist.
    if (!function_exists('runBackgroundJob')) {
        function runBackgroundJob(...$args) { }
    }

    require __DIR__ . '/../../job-worker.php';

    $output = ob_get_clean();

    // Assert that the usage message is printed.
    expect($output)->toContain("Usage: php run-job.php ClassName methodName");
});


test('runBackgroundJob helper function is defined', function () {

    // Check if the function exists
    expect(function_exists('runBackgroundJob'))->toBeTrue();

    // Check if the function is callable
    expect(is_callable('runBackgroundJob'))->toBeTrue();
});

/** @runInSeparateProcess */
test('Job is executed with all passed arguments passed', function () {

    // Set argv with valid arguments.
    $GLOBALS['argv'] = [
        'job-worker.php',
        'TestJob',
        'handle',
        'param1,param2',
        '5', // delay
        '1'  // priority
    ];

    expect(runBackgroundJob('TestJob', 'handle', ['param1,param2'], 5, 1))->toBeTrue();
});

/** @runInSeparateProcess */
test('Job is executed with all required arguments only', function () {

    // Set argv with valid arguments.
    $GLOBALS['argv'] = [
        'job-worker.php',
        'TestJob',
        'handle',
        'param1,param2'
    ];

    expect(runBackgroundJob('TestJob', 'handle', ['param1,param2']))->toBeTrue();
});
