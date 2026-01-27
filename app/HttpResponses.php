<?php

namespace App;

trait HttpResponses
{
    public function success($data = null, $message = "Request was successful", $code = 200)
    {
        return response()->json([
            'statusCode' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function error($message = "An error occurred", $code = 500, $error = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'error' => $error,
        ], $code);
    }
}
