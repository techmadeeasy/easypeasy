# Laravel App with Docker

This project is a Laravel application dockerized using PHP, Composer, and Docker. Follow the steps below to set up and run the app.

## Prerequisites

- Docker and Docker Compose installed
- Git installed

## Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/techmadeeasy/easypeasy.git
   cd easypeasy
   ```
   
2. RUN the following command to build and start the Docker containers:
   ```bash
   docker-compose up --build -d
   ```
Your app should now be built with DB and all the necessary dependencies installed. No need to run migration or any such command.
to visit the site on a browser open 


    http://localhost:9000
   
3. Basic usage of cli command 
- Run the following command to run the application: First run the worker command in the cli(this must be kept on for the jobs to be consummmed)

   ```bash
   docker-compose exec app php artisan background:work

    ```
next run the following command to execute any allowed jobs:
   
   ```php
   docker-compose exec app php job-worker.php TestJob handle "param1,param2"
   ```
Optional parameters are: 
- `delay` (in seconds): Delay before executing the job.
- `priority`: Priority of the job (lower number = higher priority).

 ```php
   docker-compose exec app php job-worker.php TestJob handle "param1,param2"  30 5
   ```

4. How to use the  runBackgroundJob method

- To use the `runBackgroundJob` method, you can call it from any controller or service class in your Laravel application. Here's an example of how to use it in a controller:

```php
<?php

namespace App\Services;

class JobService
{
    public function processJob(array $data): bool
    {
        // Trigger a background job with default delay and priority
        return runBackgroundJob('TestJob', 'handle', $data);
    }
}
```
- In this example, the `processJob` method calls the `runBackgroundJob` function, passing the job class name, method name, and data as parameters. The job will be executed in the background with a default delay and priority.

5. Steps to configure retry attempts, delays, job priorities, and security settings.

- To configure retry attempts, delays, job priorities, and security settings, you can modify the `runBackgroundJob` function in the `app/Helpers/JobHelper.php` file. Here's an example of how to set these parameters:

```php

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


```

6. Dashboard built with ability to cancel queued job 

```php
localhost:9000
```

7. Sample Test case provided in JobWorkerTestCase.php

```php
JobWorkerTestCase.php
```

8. Improvement

There are many improvements that can be made to this code, including but not limited to:

- Using named arguments in the cmd itself (e.g. `--delay=30` instead of `30`).
- Implementing a more robust error handling mechanism.
- Adding more detailed logging for job execution.
- Implementing a more sophisticated job scheduling system.
- Adding unit tests for the job worker and job classes.
- Improving the features around cancelling and resuming jobs.
- Improving a dashboard driven retries
- Improving the security around the job worker to prevent unauthorized access.
- And more...
