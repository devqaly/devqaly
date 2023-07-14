<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('created_by_id');
            $table->uuid('company_id');
            $table->string('project_key', 60)->unique();
            $table->string('title');
            $table->timestamps();

            $table
                ->foreign('created_by_id')
                ->references('id')
                ->on('users');

            $table
                ->foreign('company_id')
                ->references('id')
                ->on('companies');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
