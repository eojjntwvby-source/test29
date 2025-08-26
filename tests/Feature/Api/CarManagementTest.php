<?php

namespace Tests\Feature\Api;

use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Color;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CarManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Brand $brand;
    private CarModel $carModel;
    private Color $color;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;

        $this->brand = Brand::factory()->create();
        $this->carModel = CarModel::factory()->create(['brand_id' => $this->brand->id]);
        $this->color = Color::factory()->create();
    }

    public function test_authenticated_user_can_get_their_cars(): void
    {
        // Create cars for this user
        Car::factory()->count(3)->create(['user_id' => $this->user->id]);

        // Create cars for another user
        $anotherUser = User::factory()->create();
        Car::factory()->count(2)->create(['user_id' => $anotherUser->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson('/api/v1/cars');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    '*' => [
                        'id',
                        'brand' => ['id', 'name'],
                        'car_model' => ['id', 'name'],
                        'user_id',
                        'year',
                        'mileage',
                        'color'
                    ]
                ]
            ])
            ->assertJsonCount(3, 'data'); // Only this user's cars
    }

    public function test_authenticated_user_can_queue_car_creation(): void
    {
        Queue::fake();

        $carData = [
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
            'color_id' => $this->color->id,
            'year' => 2020,
            'mileage' => [
                'value' => 50000.5,
                'unit' => 'km'
            ],
            'color' => 'Red'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/v1/cars', $carData);

        $response->assertStatus(202)
            ->assertJson([
                'status' => 'success',
                'message' => 'Car creation has been queued for processing'
            ]);

        Queue::assertPushed(\App\Jobs\ProcessCarCreation::class);
    }

    public function test_user_cannot_create_car_with_invalid_data(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->postJson('/api/v1/cars', [
            'brand_id' => 999999, // Non-existent brand
            'car_model_id' => 999999, // Non-existent model
            'year' => 1800, // Invalid year
            'mileage' => [
                'value' => -100, // Invalid mileage value
                'unit' => 'invalid' // Invalid unit
            ]
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['brand_id', 'car_model_id', 'year', 'mileage.value', 'mileage.unit']);
    }

    public function test_authenticated_user_can_view_their_car(): void
    {
        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $car->id,
                    'user_id' => $this->user->id
                ]
            ]);
    }

    public function test_user_cannot_view_another_users_car(): void
    {
        $anotherUser = User::factory()->create();
        $car = Car::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->getJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(404)
            ->assertJson([
                'status' => 'error',
                'message' => 'Car not found or access denied'
            ]);
    }

    public function test_authenticated_user_can_queue_car_update(): void
    {
        Queue::fake();

        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id
        ]);

        $updateData = [
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id,
            'year' => 2021,
            'mileage' => [
                'value' => 30000.0,
                'unit' => 'mi'
            ],
            'color' => 'Blue'
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->putJson("/api/v1/cars/{$car->id}", $updateData);

        $response->assertStatus(202)
            ->assertJson([
                'status' => 'success',
                'message' => 'Car update has been queued for processing'
            ]);

        Queue::assertPushed(\App\Jobs\ProcessCarUpdate::class);
    }

    public function test_authenticated_user_can_delete_their_car(): void
    {
        $car = Car::factory()->create([
            'user_id' => $this->user->id,
            'brand_id' => $this->brand->id,
            'car_model_id' => $this->carModel->id
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->deleteJson("/api/v1/cars/{$car->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Car deleted successfully'
            ]);

        $this->assertDatabaseMissing('cars', ['id' => $car->id]);
    }

    public function test_unauthenticated_user_cannot_access_car_endpoints(): void
    {
        $response = $this->getJson('/api/v1/cars');
        $response->assertStatus(401);

        $response = $this->postJson('/api/v1/cars', []);
        $response->assertStatus(401);
    }
}
