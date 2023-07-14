<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_database_transactions', function (Blueprint $table) {
            // We shouldn't make this a foreign key since the `event_network_requests` might not be created yet
            $table->uuid('request_id')->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('event_database_transactions', function (Blueprint $table) {
            $table->dropColumn('request_id');
        });
    }
};
