<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\DataTransferObjects\{CreateCarData, UpdateCarData};
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CarResource;
use App\Jobs\ProcessCarCreation;
use App\Jobs\ProcessCarUpdate;
use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class CarController extends Controller
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository
    )
    {
    }

    /**
     * @OA\Get(
     *     path="/v1/cars",
     *     tags={"Cars"},
     *     summary="Список автомобилей пользователя",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Список автомобилей"),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function index(): JsonResponse
    {
        $cars = $this->carRepository->getForUser(Auth::id());

        return response()->json([
            'status' => 'success',
            'data' => CarResource::collection($cars)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/v1/cars",
     *     tags={"Cars"},
     *     summary="Создание автомобиля",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=202, description="Автомобиль добавлен в очередь"),
     *     @OA\Response(response=422, description="Ошибка валидации"),
     *     @OA\Response(response=401, description="Не авторизован")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'car_model_id' => 'required|exists:car_models,id',
            'color_id' => 'nullable|exists:colors,id',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'nullable|array',
            'mileage.value' => 'required_with:mileage|numeric|min:0',
            'mileage.unit' => 'required_with:mileage|in:km,mi',
            'color' => 'nullable|string|max:255' // Legacy field
        ]);

        $carData = CreateCarData::fromArray($validatedData, Auth::id());
        ProcessCarCreation::dispatch($carData->toArray(), Auth::id());

        return response()->json([
            'status' => 'success',
            'message' => 'Car creation has been queued for processing'
        ], 202);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/cars/{id}",
     *     tags={"Cars"},
     *     summary="Get a specific car",
     *     description="Returns details of a specific car owned by the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Данные автомобиля"),
     *     @OA\Response(response=404, description="Car not found or access denied"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function show(int $id): JsonResponse
    {
        $car = $this->carRepository->findForUser($id, Auth::id());

        if (!$car) {
            return response()->json([
                'status' => 'error',
                'message' => 'Car not found or access denied'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => new CarResource($car)
        ]);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/cars/{id}",
     *     tags={"Cars"},
     *     summary="Update a car",
     *     description="Updates a car owned by the authenticated user (asynchronous processing)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=202, description="Обновление добавлено в очередь"),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Car not found"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function update(Request $request, Car $car): JsonResponse
    {
        if ($car->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }

        $validatedData = $request->validate([
            'brand_id' => 'sometimes|exists:brands,id',
            'car_model_id' => 'sometimes|exists:car_models,id',
            'color_id' => 'nullable|exists:colors,id',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'nullable|array',
            'mileage.value' => 'required_with:mileage|numeric|min:0',
            'mileage.unit' => 'required_with:mileage|in:km,mi',
            'color' => 'nullable|string|max:255' // Legacy field
        ]);

        $updateData = UpdateCarData::fromArray($validatedData);
        if (!$updateData->hasChanges()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No changes provided'
            ], 422);
        }

        $originalData = $car->only(['brand_id', 'car_model_id', 'color_id', 'year', 'mileage_value', 'mileage_unit', 'color']);
        ProcessCarUpdate::dispatch($car, $updateData->toArray(), $originalData);

        return response()->json([
            'status' => 'success',
            'message' => 'Car update has been queued for processing'
        ], 202);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/cars/{id}",
     *     tags={"Cars"},
     *     summary="Delete a car",
     *     description="Deletes a car owned by the authenticated user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Car ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Car deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=403, description="Access denied"),
     *     @OA\Response(response=404, description="Car not found"),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function destroy(Car $car): JsonResponse
    {
        if ($car->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied'
            ], 403);
        }

        $car->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Car deleted successfully'
        ]);
    }
}
