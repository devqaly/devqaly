<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_database_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('sql');
            // We will allow execution time maximum being of 16.6 minutes.
            // This seems overly conservative but better be safe than sorry.
            $table->unsignedDouble('execution_time_in_milliseconds', 9, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_database_transactions');
    }
};
