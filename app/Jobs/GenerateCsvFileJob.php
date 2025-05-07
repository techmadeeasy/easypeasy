<?php

namespace App\Jobs;

class GenerateCsvFileJob
{
    public function handle()
    {
        sleep(10);
        $filename = time() .  '_lorem_ipsum.csv';
        $filePath = storage_path('app/public/' . $filename);


        $loremIpsum = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';

        $file = fopen($filePath, 'w');
        fputcsv($file, [$loremIpsum]);
        fclose($file);

        // Log the job execution
        \Log::info('GenerateCsvFileJob executed successfully.');

        return $this;
    }
}
