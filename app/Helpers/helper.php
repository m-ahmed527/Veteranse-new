<?php

function responseSuccess($message, $data = null)
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data' => $data,
    ], 200);
}

function responseError($message, $statusCode = 400)
{
    return response()->json([
        'success' => false,
        'message' => $message,
    ], $statusCode);
}

function responseValidationError($errors, $statusCode = 422)
{
    return response()->json([
        'success' => false,
        'message' => 'Validation Error',
        'errors' => $errors,
    ], $statusCode);
}
