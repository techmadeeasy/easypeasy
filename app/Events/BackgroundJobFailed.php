<?php
// File: app/Events/BackgroundJobFailed.php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackgroundJobFailed implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $jobId;
    public $jobClass;
    public $jobMethod;
    public $error;

    public function __construct($jobId, $jobClass, $jobMethod, $error)
    {
        $this->jobId    = $jobId;
        $this->jobClass = $jobClass;
        $this->jobMethod = $jobMethod;
        $this->error     = $error;
    }

    public function broadcastOn()
    {
        return new Channel('background-jobs');
    }

    public function broadcastAs()
    {
        return 'background-job-failed';
    }
}
