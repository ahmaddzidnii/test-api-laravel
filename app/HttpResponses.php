<?php

namespace App;

use Illuminate\Pagination\LengthAwarePaginator;

trait HttpResponses
{
    public function success($data = null, $message = "Request was successful", $code = 200)
    {
        return response()->json([
            'statusCode' => $code,
            'message' => $message,
            'data' => $data,
        ], $code, [], JSON_UNESCAPED_SLASHES);
    }

    public function error($message = "An error occurred", $code = 500, $error = null)
    {
        return response()->json([
            'statusCode' => $code,
            'message' => $message,
            'data' => $error,
        ], $code);
    }

    public function successWithPagination(
        $data,
        LengthAwarePaginator $paginator,
        string $message = "Request was successful",
        int $code = 200
    ) {
        return response()->json([
            'statusCode' => $code,
            'message'    => $message,
            'data'       => $data,
            'pagination' => [
                'currentPage' => $paginator->currentPage(),
                'perPage'     => $paginator->perPage(),
                'from'        => $paginator->firstItem(),
                'to'          => $paginator->lastItem(),
                'total'       => $paginator->total(),
                'lastPage'    => $paginator->lastPage(),
            ],
        ], $code, [], JSON_UNESCAPED_SLASHES);
    }
}
