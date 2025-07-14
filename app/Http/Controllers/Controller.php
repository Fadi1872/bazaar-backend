<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * returns a success unified response
     * 
     * @param string $message
     * @param array $data
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse(string $message = 'success',  $data = [])
    {
        $response = [
            'success' => true,
            'message' => $message
        ];

        if (!empty($data))
            $response['data'] = $data;

        return response()->json($response, 200);
    }

    /**
     * returns an error unified response
     * 
     * @param string $message
     * @param int $code
     * @param array $errors
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse(string $message = 'something went wrong!', int $code = 400, array $errors = [])
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if (!empty($errors))
            $response['date'] = $errors;

        return response()->json($response, $code);
    }
}
