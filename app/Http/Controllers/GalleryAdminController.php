<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGalleryRequest;
use App\Http\Requests\GalleryIndexRequest;
use App\Http\Resources\GalleryResource;
use App\HttpResponses;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryAdminController extends Controller
{
    use HttpResponses;
    public function index(GalleryIndexRequest $request)
    {
        $galleries = Gallery::query()
            ->search($request->search)
            ->sort($request->sort_by, $request->direction)
            ->paginate($request->per_page)
            ->withQueryString();

        return $this->successWithPagination(
            GalleryResource::collection($galleries),
            $galleries,
            'Galleries retrieved successfully'
        );
    }

    public function store(CreateGalleryRequest $request)
    {
        $data = $request->validated();

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = Str::ulid() . '.' . $file->extension();
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getClientMimeType();
            $width = getimagesize($file)[0];;
            $height = getimagesize($file)[1];
            $aspectRatio = round($width / $height, 2);
            $size = $file->getSize();

            $path = Storage::putFileAs('galleries', $file, $fileName);

            $gallery = Gallery::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'path' => $path,
                'visibility' => 'PUBLIC',
                'width' => $width,
                'height' => $height,
                'mime_type' => $mimeType,
                'size' => $size,
                'original_filename' => $originalFilename,
                'aspect_ratio' => $aspectRatio,
                'uploaded_by' => auth()->id(),
            ]);

            return $this->success(new GalleryResource($gallery), 'Gallery created successfully', 201);
        }
    }

    public function destroy($id)
    {
        $gallery = Gallery::find($id);

        if (!$gallery) {
            return $this->error('Gallery not found', 404);
        }

        // Delete the gallery record
        $gallery->delete();

        // Delete the file from storage
        Storage::delete($gallery->path);

        return $this->success(null, 'Gallery deleted successfully');
    }
}
