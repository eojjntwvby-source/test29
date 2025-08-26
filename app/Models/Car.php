<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\MileageUnit;
use App\ValueObjects\Mileage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $brand_id
 * @property int $car_model_id
 * @property int|null $color_id
 * @property int|null $user_id
 * @property int|null $year
 * @property float|null $mileage_value
 * @property MileageUnit $mileage_unit
 * @property Mileage $mileage
 * @property string|null $color
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Brand $brand
 * @property-read \App\Models\CarModel $carModel
 * @property-read \App\Models\Color|null $colorRelation
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\CarFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car forUser(int $userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereBrandId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereCarModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereColorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereMileage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereMileageUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereMileageValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Car whereYear($value)
 * @mixin \Eloquent
 */
class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'car_model_id',
        'color_id',
        'user_id',
        'year',
        'mileage_value',
        'mileage_unit',
        'color'
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage_value' => 'float',
        'mileage_unit' => MileageUnit::class,
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function colorRelation(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get mileage as Value Object
     */
    public function mileage(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->mileage_value !== null && $this->mileage_unit !== null
                ? new Mileage($this->mileage_value, $this->mileage_unit)
                : null,
            set: fn(Mileage $mileage) => [
                'mileage_value' => $mileage->value,
                'mileage_unit' => $mileage->unit,
            ]
        );
    }

    /**
     * Set mileage from array
     */
    public function setMileageFromArray(array $data): void
    {
        if (isset($data['value']) && isset($data['unit'])) {
            $this->mileage = new Mileage(
                (float)$data['value'],
                MileageUnit::from($data['unit'])
            );
        }
    }
}
