<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandResource;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tag(name="Brands", description="Car brands management")
 */
final class BrandController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/brands",
     *     tags={"Brands"},
     *     summary="Get all car brands",
     *     description="Returns a list of all available car brands",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Brand")
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $brands = Brand::all();

        return response()->json([
            'status' => 'success',
            'data' => BrandResource::collection($brands)
        ]);
    }
}
