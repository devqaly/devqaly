<?php

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
        Schema::create('event_element_scrolls', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('scroll_height');
            $table->unsignedSmallInteger('scroll_width');
            $table->unsignedSmallInteger('scroll_left');
            $table->unsignedSmallInteger('scroll_top');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_element_scrolls');
    }
};
