<?php

namespace App\Http\Controllers;

use App\Http\Requests\TechnologyIndexRequest;
use App\Http\Resources\TechnologyResource;
use App\HttpResponses;
use App\Models\Technology;

class TechnologyController extends Controller
{
    use HttpResponses;

    public function listTechStacks(TechnologyIndexRequest $request)
    {
        $technologies = Technology::query()
            ->search($request->search)
            ->sort($request->sort_by, $request->sort_dir)
            ->paginate($request->per_page)
            ->withQueryString();

        return $this->successWithPagination(
            TechnologyResource::collection($technologies),
            $technologies,
            'List of technology stacks retrieved successfully.'
        );
    }
}
