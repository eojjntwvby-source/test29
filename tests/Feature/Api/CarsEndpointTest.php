<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class CarsEndpointTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Brand $brand;
    private CarModel $carModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->brand = Brand::factory()->create();
        $this->carModel = CarModel::factory()->create(['brand_id' => $this->brand->id]);
    }

    public function test_brands_endpoint_returns_all_brands(): void
    {
        $response = $this->getJson('/api/v1/brands');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => ['id', 'name', 'created_at', 'updated_at']
                ]
            ]);
    }

    public function test_car_models_endpoint_returns_all_models(): void
    {
        $response = $this->getJson('/api/v1/car-models');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id', 'name', 'brand_id',
                        'brand' => ['id', 'name', 'created_at', 'updated_at'],
                        'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function test_cars_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/cars');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_their_cars(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->getJson('/api/v1/cars');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data'
            ]);
    }

    public function test_authenticated_user_can_create_car(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $carData = [
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
            'year' => 2022,
            'mileage' => [
                'value' => 15000,
                'unit' => 'km'
            ]
        ];

        $response = $this->postJson('/api/v1/cars', $carData);

        $response->assertStatus(202)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Car creation has been queued for processing');
    }

    public function test_authenticated_user_can_view_their_car(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response = $this->getJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id', 'brand', 'car_model', 'year', 'mileage', 'user_id',
                    'created_at', 'updated_at'
                ]
            ]);
    }

    public function test_authenticated_user_can_update_their_car(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $updateData = [
            'year' => 2023,
            'mileage' => [
                'value' => 20000,
                'unit' => 'km'
            ]
        ];

        $response = $this->putJson("/api/v1/cars/{$car->id}", $updateData);

        $response->assertStatus(202)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Car update has been queued for processing');
    }

    public function test_authenticated_user_can_delete_their_car(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response = $this->deleteJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('message', 'Car deleted successfully');
    }

    public function test_user_cannot_access_other_users_cars(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $otherUser = User::factory()->create();
        $car = Car::factory()->create([
            'user_id' => $otherUser->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
        ]);

        $response = $this->getJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(404);
    }

    public function test_car_creation_validates_required_fields(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/v1/cars', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand_id', 'car_model_id']);
    }

    public function test_car_creation_validates_brand_exists(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $carData = [
            'brand_id' => 99999,
            'car_model_id' => $this->carModel->id,
            'year' => 2022,
        ];

        $response = $this->postJson('/api/v1/cars', $carData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand_id']);
    }

    public function test_car_creation_validates_model_exists(): void
    {
        $this->actingAs($this->user, 'sanctum');

        $carData = [
            'brand_id' => $this->brand->id,
            'car_model_id' => 99999,
            'year' => 2022,
        ];

        $response = $this->postJson('/api/v1/cars', $carData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['car_model_id']);
    }
}
