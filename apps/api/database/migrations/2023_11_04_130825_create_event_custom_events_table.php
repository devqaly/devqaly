<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_custom_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->json('payload')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_custom_events');
    }
};
