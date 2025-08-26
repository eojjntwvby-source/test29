<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarModelResource;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Car Models", description="Car models management")
 */
final class CarModelController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/car-models",
     *     tags={"Car Models"},
     *     summary="Get all car models",
     *     description="Returns a list of car models, optionally filtered by brand",
     *     @OA\Parameter(
     *         name="brand_id",
     *         in="query",
     *         description="Filter by brand ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/CarModel")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = CarModel::query()->with('brand');

        if ($request->has('brand_id')) {
            $query->where('brand_id', (int)$request->brand_id);
        }

        $models = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => CarModelResource::collection($models)
        ]);
    }
}
