<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_resize_screens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('inner_width');
            $table->unsignedSmallInteger('inner_height');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_resize_screens');
    }
};
