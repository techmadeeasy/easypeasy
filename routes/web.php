<?php

use App\Http\Controllers\BackgroundJobController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BackgroundJobController::class, 'index'])->name('background-jobs.index');

