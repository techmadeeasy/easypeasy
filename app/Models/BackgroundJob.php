<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackgroundJob extends Model
{
    protected $fillable = [
        'job_class',
        'job_method',
        'payload',
        'priority',
        'available_at',
        'max_attempts',
    ];
    protected $dates = [
        'available_at',
        'reserved_at',
        'completed_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('background-jobs.table');
    }
}
