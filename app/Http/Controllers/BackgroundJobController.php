<?php

namespace App\Http\Controllers;

use App\Models\BackgroundJob;
use Illuminate\Http\Request;

class BackgroundJobController extends Controller
{
    public function index()
    {
        return view('background-jobs.index');
    }
}
