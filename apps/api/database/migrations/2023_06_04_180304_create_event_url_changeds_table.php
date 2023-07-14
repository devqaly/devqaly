<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_url_changes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->text('to_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_url_changes');
    }
};
