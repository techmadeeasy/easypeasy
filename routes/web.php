<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    runBackgroundJob(
        'TestJob',
        'handle',
        ['param1', 'param2']
    );
//    return view('welcome');
});
