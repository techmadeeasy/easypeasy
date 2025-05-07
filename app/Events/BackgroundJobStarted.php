<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BackgroundJobStarted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobId;
    public $jobClass;
    public $jobMethod;

    public function __construct($jobId, $jobClass, $jobMethod)
    {
        $this->jobId    = $jobId;
        $this->jobClass = $jobClass;
        $this->jobMethod = $jobMethod;
    }

    public function broadcastOn()
    {
        return new Channel('background-jobs');
    }

    // Optionally define a broadcast event name
    public function broadcastAs()
    {
        return 'background-job-started';
    }
}
