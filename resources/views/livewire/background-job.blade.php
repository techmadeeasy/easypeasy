<div class="overflow-x-auto shadow-md sm:rounded-lg w-full max-w-7xl">
    <div>
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg" role="alert">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('error'))
            <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
                {{ session('error') }}
            </div>
        @endif
    </div>
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-white uppercase bg-gray-700">
        <tr>
            <th scope="col" class="px-6 py-3">Job id</th>
            <th scope="col" class="px-6 py-3">Class name</th>
            <th scope="col" class="px-6 py-3">Method</th>
            <th scope="col" class="px-6 py-3">Status</th>
            <th scope="col" class="px-6 py-3">Retries</th>
            <th scope="col" class="px-6 py-3">Error message</th>
            <th scope="col" class="px-6 py-3">Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($jobs as $job)
            <tr class="border-b hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-700">
                <td class="px-6 py-4">{{ $job->id }}</td>
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $job->job_class }}
                </th>
                <td class="px-6 py-4">{{ $job->job_method }}</td>
                <td class="px-6 py-4">
                    @if($job->status === 'success')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ $job->status }}
                        </span>
                    @elseif($job->status === 'failure')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            {{ $job->status }}
                        </span>
                    @elseif($job->status === 'running')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $job->status }}
                        </span>
                    @elseif($job->status === 'cancelled')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        {{ $job->status }}
                    </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
            {{ $job->status }}
        </span>
                    @endif
                </td>
                <td class="px-6 py-4">{{ $job->attempts }}</td>
                <td class="px-6 py-4">{{ $job->last_error }}</td>
                <td class="px-6 py-4">
                    @if($job->status == 'queued')
                        <button class="font-medium text-blue-600 hover:underline" wire:click="cancelJob({{ $job->id }})" >Cancel Job</button>
                    @else
                        <span class="font-medium text-gray-400 cursor-not-allowed">Cancel Job</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="my-4 flex justify-end">
        {{ $jobs->links() }}
    </div>
    <script>
        window.addEventListener('background-job-completed', event => {
            Livewire.emit('backgroundJobCompleted');
        });
        window.addEventListener('background-job-failed', event => {
            Livewire.emit('backgroundJobFailed');
        });
        window.addEventListener('background-job-started', event => {
            Livewire.emit('backgroundJobStarted');
        });
    </script>
</div>
