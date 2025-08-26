<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ColorResource;
use App\Models\Color;
use Illuminate\Http\JsonResponse;

final class ColorController extends Controller
{
    public function index(): JsonResponse
    {
        $colors = Color::all();

        return response()->json([
            'status' => 'success',
            'data' => ColorResource::collection($colors)
        ]);
    }

    public function show(Color $color): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new ColorResource($color)
        ]);
    }
}
