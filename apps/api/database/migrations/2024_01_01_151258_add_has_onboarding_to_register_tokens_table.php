<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('register_tokens', function (Blueprint $table) {
            $table->boolean('has_onboarding')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('register_tokens', function (Blueprint $table) {
            $table->dropColumn('has_onboarding');
        });
    }
};
