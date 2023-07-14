<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('level', 50);
            $table->text('log');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_logs');
    }
};
