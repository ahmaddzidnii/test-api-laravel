<?php

namespace App;

trait HttpResponses
{
    public function success($data = null, $message = "Request was successful", $code = 200)
    {
        return response()->json([
            'statusCode' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public function error($message = "An error occurred", $code = 500, $error = null)
    {
        return response()->json([
            'statusCode' => $code,
            'message' => $message,
            'data' => $error,
        ], $code);
    }
}
