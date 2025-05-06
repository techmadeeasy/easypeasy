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
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $job->job_class }}
                </th>
                <td class="px-6 py-4">{{ $job->job_method }}</td>
                <td class="px-6 py-4">{{ $job->status }}</td>
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
    <div class="mt-4 flex justify-end">
        {{ $jobs->links() }}
    </div>
</div>
