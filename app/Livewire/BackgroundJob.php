<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class BackgroundJob extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';

    public function render()
    {
        $jobs = $this->loadJobs();
        return view('livewire.background-job', [
            'jobs' => $jobs,
        ]);
    }

    public function cancelJob($jobId)
    {
        $job = \App\Models\BackgroundJob::find($jobId);
        if ($job) {
            $job->update(['status', \App\JobStatusEnum::CANCELLED->value]);
            $this->loadJobs();
            session()->flash('message', 'Job cancelled successfully.');
        } else {
            session()->flash('error', 'Job not found.');
        }

        $this->resetPage();
    }

    public function loadJobs()
    {
        return \App\Models\BackgroundJob::orderBy('priority')
            ->orderByDesc('available_at')
            ->paginate(10);
    }
}
