<?php

use App\JobStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('background_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('job_class');
            $table->string('job_method');
            $table->text('payload')->nullable();
            $table->unsignedTinyInteger('priority')->default(config('background-jobs.default_priority'));
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedTinyInteger('max_attempts')->default(config('background-jobs.retry_attempts'));
            $table->timestamp('available_at')->nullable()->index();
            $table->timestamp('reserved_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable()->index();
            $table->text('last_error')->nullable();
            $table->enum('status', JobStatusEnum::values())
                ->default(JobStatusEnum::QUEUEUED->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('background_jobs');
    }
};
