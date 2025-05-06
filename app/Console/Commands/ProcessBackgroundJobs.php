<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProcessBackgroundJobs extends Command
{
    protected $signature = 'background:work {--sleep=5 : Seconds to sleep when no job is found}';
    protected $description = 'Process pending background jobs from the database';

    public function handle()
    {
        $sleep = (int) $this->option('sleep');

        while (true) {
            // Fetch next available job, ordered by priority
            $job = DB::table(config('background-jobs.table'))
                ->whereNull('reserved_at')
                ->whereNull('completed_at')
                ->where('available_at', '<=', Carbon::now())
                ->orderBy('priority', 'asc')
                ->orderBy('available_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($job) {
                // Mark as reserved
                DB::table(config('background-jobs.table'))
                    ->where('id', $job->id)
                    ->update(['reserved_at' => Carbon::now()]);

                try {
                    $params   = json_decode($job->payload, true) ?: [];
                    $instance = new $job->job_class();
                    error_log($instance);
                    call_user_func_array([$instance, $job->job_method], $params);

                    // Mark as completed
                    DB::table(config('background-jobs.table'))
                        ->where('id', $job->id)
                        ->update(['completed_at' => Carbon::now()]);
                } catch (\Throwable $e) {
                    $attempts      = $job->attempts + 1;
                    $nextAvailable = Carbon::now()->addSeconds(config('background-jobs.retry_delay'));

                    $updates = [
                        'attempts'      => $attempts,
                        'last_error'    => $e->getMessage(),
                        'reserved_at'   => null,
                    ];

                    if ($attempts < $job->max_attempts) {
                        $updates['available_at'] = $nextAvailable;
                    } else {
                        // Give up and mark complete
                        $updates['completed_at'] = Carbon::now();
                    }

                    DB::table(config('background-jobs.table'))
                        ->where('id', $job->id)
                        ->update($updates);
                }
            } else {
                sleep($sleep);
            }
        }
    }
}
