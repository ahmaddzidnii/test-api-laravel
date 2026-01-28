<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\HttpResponses;
use App\Models\Project;

class ProjectController extends Controller
{
    use HttpResponses;

    public function listProjects()
    {
        $projects = Project::with(['technologies', 'thumbnail'])->paginate(10);
        return $this->successWithPagination(ProjectResource::collection($projects), $projects, 'Projects retrieved successfully');
    }

    public function createProject(CreateProjectRequest $request)
    {
        $this->authorize('create', Project::class);

        $requestedData = $request->validated();

        /**
         *  Endpoint must be idempotent, if the same request is sent multiple times,
         */

        $project = Project::updateOrCreate(
            ['slug' => $requestedData['slug']],
            [
                'title' => $requestedData['title'],
                'description' => $requestedData['description'],
                'link_demo' => $requestedData['linkDemo'] ?? null,
                'duration' => $requestedData['duration'] ?? null,
                'launch_year' => $requestedData['launchYear'] ?? null,
                'user_id' => auth()->id(),
            ]
        );
        return $this->success(new ProjectResource($project), 'Project created successfully');
    }

    public function getProjectByIdOrSlug($projectId)
    {
        $project = Project::with(['technologies', 'thumbnail', 'images', 'keyFeatures', 'challenges', 'results', 'testimonials'])
            ->where('id', $projectId)
            ->orWhere('slug', $projectId)
            ->first();

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        return $this->success(new ProjectResource($project), 'Project retrieved successfully');
    }

    public function changeProjectSlug($id)
    {
        $this->authorize('update', Project::class);

        $project = Project::find($id);
        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $newSlug = request()->input('slug');

        if (!$newSlug) {
            return $this->error('New slug is required', 400);
        }

        if (Project::where('slug', $newSlug)->exists()) {
            return $this->error('Slug already in use', 409);
        }

        $project->slug = $newSlug;
        $project->save();

        return $this->success(new ProjectResource($project), 'Project slug updated successfully');
    }

    public function deleteProject($projectId)
    {
        $this->authorize('delete', Project::class);

        $project = Project::find($projectId);
        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $project->delete();

        return $this->success(null, 'Project deleted successfully');
    }

    public function updateProjectBasicInfo($projectId)
    {
        // Implementation for updating project basic info
    }

    public function syncProjectTechnologies($projectId)
    {
        // Implementation for syncing project technologies
    }

    public function syncProjectDetails($projectId)
    {
        // Implementation for syncing project details
    }

    public function createProjectTestimonial($projectId)
    {
        // Implementation for creating project testimonial
    }

    public function deleteProjectTestimonial($projectId, $testimonialId)
    {
        // Implementation for deleting project testimonial
    }

    public function updateProjectTestimonial($projectId, $testimonialId)
    {
        // Implementation for updating project testimonial
    }
}
