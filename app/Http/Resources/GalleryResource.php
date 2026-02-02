<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class GalleryResource extends JsonResource
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
            'path' => $this->path,
            'url' => url(Storage::url($this->path)),
            'visibility' => $this->visibility,
            'width' => $this->width,
            'height' => $this->height,
            'mimeType' => $this->mime_type,
            'size' => $this->size,
            'originalFilename' => $this->original_filename,
            'aspectRatio' => $this->aspect_ratio,
            'uploadedById' => $this->uploaded_by,
            'uploader' => new UserResource($this->whenLoaded('uploader')),
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
