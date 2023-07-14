<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('register_tokens', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('token');
            $table->string('email');
            $table->dateTime('used_at')->nullable();
            $table->boolean('revoked')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('register_tokens');
    }
};
