<?php

use App\Enum\Sessions\SessionVideoStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('created_by_id')->nullable();
            $table->uuid('project_id');
            $table->unsignedSmallInteger('window_width');
            $table->unsignedSmallInteger('window_height');
            $table->string('os');
            $table->string('platform_name');
            $table->string('version');
            $table->string('video_status')->default(SessionVideoStatusEnum::IN_QUEUE_FOR_CONVERSION->value);
            $table->string('video_extension', 10)->nullable();
            $table->double('video_size_in_megabytes', 7, 2)->nullable();
            $table->unsignedTinyInteger('video_conversion_percentage')->default(0);
            $table->unsignedSmallInteger('video_duration_in_seconds')->nullable();
            $table->dateTime('started_video_conversion_at')->nullable();
            $table->dateTime('ended_video_conversion_at')->nullable();
            $table->string('secret_token', 60);
            $table->timestamps();

            $table->foreign('created_by_id')->references('id')->on('users');
            $table->foreign('project_id')->references('id')->on('projects');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }
};
