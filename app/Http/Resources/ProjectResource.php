<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'about' => $this->about,
            'slug' => $this->slug,
            'duration' => $this->duration,
            'launchYear' => $this->launch_year,
            'demoUrl' => $this->demo_url,
            'status' => $this->status,
            'thumbnail' => $this->whenLoaded('thumbnail', fn() => new ProjectImageResource($this->thumbnail)),
            'technologies' => TechnologyResource::collection($this->whenLoaded('technologies')),
            'images' => ProjectImageResource::collection($this->whenLoaded('images')),
            'keyFeatures' => ProjectKeyFeaturesResource::collection($this->whenLoaded('keyFeatures')),
            'challenges' => ProjectChallegesResource::collection($this->whenLoaded('challenges')),
            'results' => ProjectResultsResource::collection($this->whenLoaded('results')),
            'testimonials' => ProjectTestimonialResource::collection($this->whenLoaded('testimonials')),
            'userId' => $this->user_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
