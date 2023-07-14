<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_element_clicks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedSmallInteger('position_x');
            $table->unsignedSmallInteger('position_y');
            $table->string('element_classes')->nullable();
            $table->string('inner_text')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_element_clicks');
    }
};
