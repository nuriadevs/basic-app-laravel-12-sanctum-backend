<?php

namespace App\Http\Response;

/**
 * Class ApiResponse
 *
 * Provides methods for generating API responses.
 */
class ApiResponse
{

    /**
     * Returns a success response.
     *
     * @param string $message The success message.
     * @param int $statusCode The HTTP status code of the response.
     * @param array $data Additional data to include in the response.
     * @return \Illuminate\Http\Response The HTTP success response.
     */

    public static function success($message = 'Success', $statusCode = 200, $data = [])
    {

        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'error' => false,
            'data' => $data
        ], $statusCode);
    }


    /**
     * Returns an error response.
     *
     * @param string $message The error message.
     * @param int $statusCode The HTTP status code of the response.
     * @param array $data Additional data to include in the response.
     * @return \Illuminate\Http\Response The HTTP error response.
     */

    public static function error($message = 'Error', $statusCode, $data = [])
    {

        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'error' => true,
            'data' => $data
        ], $statusCode);
    }

    /**
     * Returns a registration response.
     * @param string $message The registration message.
     * @param int $statusCode The HTTP status code of the response.
     * @param array $data Additional data to include in the response.
     * @return \Illuminate\Http\Response The HTTP registration response.
     */
    public static function register($message = 'User registered successfully', $statusCode = 201, $data = [])
    {
        return response()->json([
            'message' => $message,
            'statusCode' => $statusCode,
            'error' => false,
            'data' => $data
        ], $statusCode);
    }
}
