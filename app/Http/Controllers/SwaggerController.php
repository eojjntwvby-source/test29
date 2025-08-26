<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Car Management API",
 *     version="1.0.0",
 *     description="API для управления автомобилями"
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8080",
 *     description="Development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
final class SwaggerController extends Controller
{
    // Этот класс используется только для Swagger аннотаций
}
