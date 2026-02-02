<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryIndexRequest;
use App\Http\Resources\GalleryResource;
use App\HttpResponses;
use App\Models\Gallery;

class GalleryController extends Controller
{
    use HttpResponses;
    public function __invoke(GalleryIndexRequest $request)
    {
        $galleries = Gallery::query()
            ->where('visibility', 'PUBLIC')
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
}
