<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
 * @OA\Info(
 *    title="Laravel IAM Swagger API",
 *    version="1.0.0",
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     securityScheme="bearerAuth",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 *
 */
    public function response(int $status, bool $error, string $message = '', array|object $data = []): object
    {
        return response()->json([
            'status' => $status,
            'error' => $error,
            'message' => $message,
            'data' => $data
        ], $status);
    }
}
