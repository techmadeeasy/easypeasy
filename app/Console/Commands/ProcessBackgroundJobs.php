<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\JobStatusEnum;

class ProcessBackgroundJobs extends Command
{
    protected $signature = 'background:work {--sleep=5 : Seconds to sleep when no job is found}';
    protected $description = 'Process pending background jobs from the database';

    public function handle()
    {
        $sleep = (int) $this->option('sleep');

        while (true) {
            $job = DB::table(config('background-jobs.table'))
                ->whereNull('reserved_at')
                ->whereNull('completed_at')
                ->where('available_at', '<=', Carbon::now())
                ->where('status','<>', JobStatusEnum::CANCELLED->value)
                ->orderBy('priority', 'asc')
                ->orderBy('available_at', 'asc')
                ->lockForUpdate()
                ->first();

            if ($job) {

                event(new \App\Events\BackgroundJobStarted($job->id, $job->job_class, $job->job_method));
                // Mark as reserved and update status to running
                DB::table(config('background-jobs.table'))
                    ->where('id', $job->id)
                    ->update([
                        'reserved_at' => Carbon::now(),
                        'status'      => JobStatusEnum::RUNNING->value,
                    ]);

                try {
                    $params   = json_decode($job->payload, true) ?: [];
                    $instance = new $job->job_class();
                    call_user_func_array([$instance, $job->job_method], $params);

                    // Mark as completed and update status to success
                    DB::table(config('background-jobs.table'))
                        ->where('id', $job->id)
                        ->update([
                            'completed_at' => Carbon::now(),
                            'status'       => JobStatusEnum::SUCCESS->value,
                        ]);

                    event(new \App\Events\BackgroundJobCompleted($job->id, $job->job_class, $job->job_method));
                    error_log("[" . now() . "] " . $job->job_class . "@" . $job->job_method . " success" . PHP_EOL, 3, storage_path('logs/background_jobs_errors.log'));
                } catch (\Throwable $e) {
                    $attempts      = $job->attempts + 1;
                    $nextAvailable = Carbon::now()->addSeconds(config('background-jobs.retry_delay'));

                    $updates = [
                        'attempts'    => $attempts,
                        'last_error'  => $e->getMessage(),
                        'reserved_at' => null,
                    ];

                    if ($attempts < $job->max_attempts) {
                        $updates['available_at'] = $nextAvailable;
                        $updates['status']       = JobStatusEnum::QUEUEUED->value;
                    } else {
                        $updates['completed_at'] = Carbon::now();
                        $updates['status']       = JobStatusEnum::FAILURE->value;
                    }

                    DB::table(config('background-jobs.table'))
                        ->where('id', $job->id)
                        ->update($updates);
// In the catch block for a failed job:
                    event(new \App\Events\BackgroundJobFailed($job->id, $job->job_class, $job->job_method, $e->getMessage()));
                    error_log("[" . now() . "] " . $job->job_class . "@" . $job->job_method . " failure: " . $e->getMessage() . PHP_EOL, 3, storage_path('logs/background_jobs_errors.log'));
                }
            } else {
                sleep($sleep);
            }
        }
    }
}
