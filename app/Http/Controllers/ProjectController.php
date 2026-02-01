<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectIndexRequest;
use App\Http\Resources\ProjectResource;
use App\HttpResponses;
use App\Models\Project;

class ProjectController extends Controller
{
    use HttpResponses;

    public function listProjects(ProjectIndexRequest $request)
    {
        $projects = Project::query()
            ->where('status', 'PUBLIC')
            ->with(['technologies', 'thumbnail'])
            ->search($request->search)
            ->sort($request->sort_by, $request->sort_dir)
            ->paginate($request->per_page)
            ->withQueryString();

        return $this->successWithPagination(
            ProjectResource::collection($projects),
            $projects,
            'Projects retrieved successfully'
        );
    }

    public function getProjectByIdOrSlug($projectId)
    {
        $project = Project::with(['technologies', 'thumbnail', 'images', 'keyFeatures', 'challenges', 'results', 'testimonials'])
            ->where('status', 'PUBLIC')
            ->where(function ($query) use ($projectId) {
                if (ctype_digit($projectId)) {
                    $query->where('id', $projectId);
                } else {
                    $query->where('slug', $projectId);
                }
            })
            ->first();

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        return $this->success(new ProjectResource($project), 'Project retrieved successfully');
    }
}
