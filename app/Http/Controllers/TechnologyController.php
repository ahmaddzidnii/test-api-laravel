<?php

namespace App\Http\Controllers;

use App\Http\Requests\TechnologyIndexRequest;
use App\Http\Resources\TechnologyResource;
use App\HttpResponses;
use App\Models\Technology;
use Illuminate\Http\Request;

class TechnologyController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     */
    public function index(TechnologyIndexRequest $request)
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
