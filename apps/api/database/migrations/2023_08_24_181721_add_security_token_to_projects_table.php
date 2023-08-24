<?php

use App\Models\Project\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table
                ->string('security_token', 60)
                ->after('project_key')
                ->unique()
                ->nullable();
        });

        Project::query()->chunk(50, function (Collection $projects) {
            $projects->each(function (Project $project) {
                $project->project_key = Str::random(60);
                $project->save();
            });
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('security_token');
        });
    }
};
