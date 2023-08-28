<?php

namespace App\services\Resources;

use App\Models\Company\Company;
use App\Models\Project\Project;
use App\Models\User;
use App\Traits\UsesPaginate;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProjectService
{
    use UsesPaginate;

    public function createProject(
        Collection $data,
        User $creator,
        Company $company,
    ): Project
    {
        /** @var Project $project */
        $project = Project::create([
            'title' => $data->get('title'),
            'created_by_id' => $creator->id,
            'company_id' => $company->id,
        ]);

        return $project;
    }

    public function listProject(Collection $filters, Company $company)
    {
        $projects = Project::where('company_id', $company->id);

        if ($filters->has('loadUrls')) {
            $projects->with('urls');
        }

        if ($filters->has('title')) {
            $projects->where('title', 'LIKE', $filters->get('title') . '%');
        }

        return $projects->paginate($this->getPerPage());
    }

    public function revokeProjectSecurityToken(Project $project): Project
    {
        $newSecurityToken = Str::random(60);

        if (Project::where('security_token', $newSecurityToken)->exists()) {
            $newSecurityToken = Str::random(60);
        }

        $project->security_token = $newSecurityToken;
        $project->save();

        return $project;
    }
}
