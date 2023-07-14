<?php

namespace App\Http\Controllers\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resources\Project\IndexProjectRequest;
use App\Http\Requests\Resources\Project\StoreProjectRequest;
use App\Http\Resources\Resources\ProjectResource;
use App\Models\Company\Company;
use App\Models\Project\Project;
use App\services\Resources\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    public function index(IndexProjectRequest $request, Company $company, ProjectService $projectService): AnonymousResourceCollection
    {
        $projects = $projectService->listProject(
            collect($request->validated()),
            $company
        );

        return ProjectResource::collection($projects);
    }

    public function store(
        StoreProjectRequest $request,
        ProjectService $projectService,
        Company $company
    ): ProjectResource
    {
        $project = $projectService->createProject(
            collect($request->validated()),
            $request->user(),
            $company,
        );

        return new ProjectResource($project);
    }

    public function show(Project $project): ProjectResource
    {
        $this->authorize('view', $project);

        return new ProjectResource($project);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
