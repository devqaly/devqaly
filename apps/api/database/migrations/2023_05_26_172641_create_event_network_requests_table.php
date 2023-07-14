<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_network_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('method', 20);
            $table->text('url');
            $table->uuid('request_id');
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('request_headers')->nullable();
            $table->text('request_body')->nullable();
            $table->text('response_headers')->nullable();
            $table->text('response_body')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_network_requests');
    }
};
