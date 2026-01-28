<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'thumbnail' => $this->thumbnail?->image_url,
            'technologies' => $this->whenLoaded('technologies'),
            'images' => $this->whenLoaded('images'),
            'keyFeatures' => $this->whenLoaded('keyFeatures'),
            'challenges' => $this->whenLoaded('challenges'),
            'results' => $this->whenLoaded('results'),
            'testimonials' => $this->whenLoaded('testimonials'),
            'userId' => $this->user_id,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
