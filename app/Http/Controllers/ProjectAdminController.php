<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateProjectRequest;
use App\Http\Requests\CreateProjectTestimonialRequest;
use App\Http\Requests\ProjectIndexRequest;
use App\Http\Requests\SetPrimaryImageRequest;
use App\Http\Requests\SyncProjectDetailsRequest;
use App\Http\Requests\SyncProjectTechnologiesRequest;
use App\Http\Requests\UpdateProjectBasicInfoRequest;
use App\Http\Requests\UpdateProjectTestimonialRequest;
use App\Http\Resources\ProjectImageResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\ProjectTestimonialResource;
use App\HttpResponses;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectAdminController extends Controller
{
    use HttpResponses;

    public function listProjects(ProjectIndexRequest $request)
    {
        $projects = Project::query()
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

    public function changeSlug($id)
    {
        $project = Project::find($id);
        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

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
        $project = Project::find($projectId);
        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('delete', $project);

        $project->delete();

        return $this->success(null, 'Project deleted successfully');
    }

    public function updateBasicInfo(UpdateProjectBasicInfoRequest $request, $projectId)
    {

        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $requestedData = $request->validated();

        $this->authorize('update', $project);

        // Map camelCase keys to snake_case for database
        $updateData = [];

        if (isset($requestedData['title'])) {
            $updateData['title'] = $requestedData['title'];
        }

        if (isset($requestedData['description'])) {
            $updateData['description'] = $requestedData['description'];
        }

        if (isset($requestedData['about'])) {
            $updateData['about'] = $requestedData['about'];
        }

        if (isset($requestedData['status'])) {
            $updateData['status'] = $requestedData['status'];
        }

        if (isset($requestedData['duration'])) {
            $updateData['duration'] = $requestedData['duration'];
        }

        if (isset($requestedData['launchYear'])) {
            $updateData['launch_year'] = $requestedData['launchYear'];
        }

        if (isset($requestedData['demoUrl'])) {
            $updateData['demo_url'] = $requestedData['demoUrl'];
        }

        // Update project
        $project->update($updateData);

        return $this->success(
            new ProjectResource($project),
            'Project basic info updated successfully'
        );
    }

    public function syncTechnologies(SyncProjectTechnologiesRequest $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $requestedData = $request->validated();

        // Extract only technology IDs from the request
        $technologyIds = collect($requestedData['technologies'])
            ->pluck('id')
            ->toArray();

        // Sync technologies (this will add new ones and remove ones not in the array)
        $project->technologies()->sync($technologyIds);

        // Load the updated project with technologies
        $project->load('technologies');

        return $this->success(
            new ProjectResource($project),
            'Project technologies synced successfully'
        );
    }

    public function syncDetails(SyncProjectDetailsRequest $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $requestedData = $request->validated();

        DB::transaction(function () use ($project, $requestedData) {
            // Update about project if provided
            if (isset($requestedData['aboutProject'])) {
                $project->update(['about' => $requestedData['aboutProject']]);
            }

            // Sync features: delete all then create new
            if (isset($requestedData['features'])) {
                $project->keyFeatures()->delete();
                foreach ($requestedData['features'] as $feature) {
                    $project->keyFeatures()->create([
                        'feature' => $feature['feature']
                    ]);
                }
            }

            // Sync challenges: delete all then create new
            if (isset($requestedData['challenges'])) {
                $project->challenges()->delete();
                foreach ($requestedData['challenges'] as $challenge) {
                    $project->challenges()->create([
                        'challenge' => $challenge['challenge']
                    ]);
                }
            }

            // Sync results: delete all then create new
            if (isset($requestedData['results'])) {
                $project->results()->delete();
                foreach ($requestedData['results'] as $result) {
                    $project->results()->create([
                        'result' => $result['result']
                    ]);
                }
            }
        });

        // Reload relationships
        $project->load(['keyFeatures', 'challenges', 'results']);

        return $this->success(
            new ProjectResource($project),
            'Project details synced successfully'
        );
    }

    public function createTestimonial(CreateProjectTestimonialRequest $request, $projectId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $requestedData = $request->validated();

        $testimonial = $project->testimonials()->create([
            'name' => $requestedData['name'],
            'role' => $requestedData['role'],
            'testimonial' => $requestedData['testimonial'],
            'rating' => $requestedData['rating'],
            'avatar_url' => $requestedData['avatarUrl'] ?? null,
        ]);

        return $this->success(
            new ProjectTestimonialResource($testimonial),
            'Testimonial created successfully'
        );
    }

    public function deleteTestimonial($projectId, $testimonialId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $testimonial = $project->testimonials()->find($testimonialId);

        if (!$testimonial) {
            return $this->error('Testimonial not found', 404);
        }

        $testimonial->delete();

        return $this->success(null, 'Testimonial deleted successfully');
    }

    public function updateTestimonial(UpdateProjectTestimonialRequest $request, $projectId, $testimonialId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $testimonial = $project->testimonials()->find($testimonialId);

        if (!$testimonial) {
            return $this->error('Testimonial not found', 404);
        }

        $requestedData = $request->validated();

        // Map camelCase to snake_case for partial update
        $updateData = [];

        if (isset($requestedData['name'])) {
            $updateData['name'] = $requestedData['name'];
        }

        if (isset($requestedData['role'])) {
            $updateData['role'] = $requestedData['role'];
        }

        if (isset($requestedData['testimonial'])) {
            $updateData['testimonial'] = $requestedData['testimonial'];
        }

        if (isset($requestedData['rating'])) {
            $updateData['rating'] = $requestedData['rating'];
        }

        if (isset($requestedData['avatarUrl'])) {
            $updateData['avatar_url'] = $requestedData['avatarUrl'];
        }

        $testimonial->update($updateData);

        return $this->success(
            new ProjectTestimonialResource($testimonial),
            'Testimonial updated successfully'
        );
    }

    public function uploadImage(Request $request, $projectId)
    {
        $request->validate([
            'image' => [
                'required',
                'image',
                'mimetypes:image/jpeg,image/png,image/webp',
                'max:10240',
            ],
        ]);

        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $file = $request->file('image');

        try {
            return DB::transaction(function () use ($request, $project) {
                $file = $request->file('image');

                $originalFilename = $file->getClientOriginalName();
                $extension = $file->extension();
                $fileSize = $file->getSize();

                // Generate unique filename
                $filename = Str::ulid() . '.' . $extension;

                // Store file
                $path = Storage::putFileAs('project-images', $file, $filename);

                // Save to database 
                $projectImage = $project->images()->create([
                    'path' => $path,
                    'file_name' => $originalFilename,
                    'file_type' => $extension,
                    'file_size' => $fileSize,
                    'is_primary' => false,
                    'is_used' => true,
                ]);

                $response = new ProjectImageResource($projectImage);

                return $this->success($response, 'Image uploaded successfully');
            });
        } catch (\Exception $e) {
            // If transaction fails, delete uploaded file if exists
            if (isset($path) && Storage::exists($path)) {
                Storage::delete($path);
            }

            return $this->error('Failed to upload image: ' . $e->getMessage(), 500);
        }
    }

    public function setPrimaryImage(SetPrimaryImageRequest $request, $projectId, $imageId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $image = $project->images()->find($imageId);

        if (!$image) {
            return $this->error('Image not found or does not belong to this project', 404);
        }

        DB::transaction(function () use ($project, $image) {
            // Set all images to non-primary
            $project->images()->update(['is_primary' => false]);

            // Set this image as primary
            $image->update(['is_primary' => true]);
        });

        return $this->success(
            new ProjectImageResource($image->fresh()),
            'Primary image set successfully'
        );
    }

    public function deleteImage($projectId, $imageId)
    {
        $project = Project::find($projectId);

        if (!$project) {
            return $this->error('Project not found', 404);
        }

        $this->authorize('update', $project);

        $image = $project->images()->find($imageId);

        if (!$image) {
            return $this->error('Image not found', 404);
        }

        DB::transaction(function () use ($project, $image) {
            $wasPrimary = $image->is_primary;

            // Delete file from storage
            if (Storage::exists($image->path)) {
                Storage::delete($image->path);
            }

            // Delete from database
            $image->delete();

            // If deleted image was primary, set a random remaining image as primary
            if ($wasPrimary) {
                $remainingImage = $project->images()->inRandomOrder()->first();

                if ($remainingImage) {
                    $remainingImage->update(['is_primary' => true]);
                }
            }
        });

        return $this->success(null, 'Image deleted successfully');
    }

    public function updateVisibilityBatch(Request $request)
    {
        $data = $request->validate([
            'project_ids'   => ['required', 'array', 'min:1'],
            'project_ids.*' => ['integer', 'exists:projects,id'],
            'visibility'     => ['required', 'in:PUBLIC,PRIVATE'],
        ]);


        $projects = Project::whereIn('id', $data['project_ids'])->get();

        foreach ($projects as $project) {
            $this->authorize('update', $project);
        }

        Project::whereIn('id', $data['project_ids'])
            ->update([
                'status' => $data['visibility'],
            ]);


        $updatedIds = $projects->pluck('id')->toArray();

        $response = [
            'updatedProjectIds' => $updatedIds,
            'visibility' => $data['visibility'],
        ];

        return $this->success($response, 'Project visibility updated successfully');
    }
}
