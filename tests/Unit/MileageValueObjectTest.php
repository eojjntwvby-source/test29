<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\MileageUnit;
use App\ValueObjects\Mileage;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

final class MileageValueObjectTest extends TestCase
{
    public function test_can_create_mileage_in_kilometers(): void
    {
        $mileage = Mileage::kilometers(50000.5);

        $this->assertEquals(50000.5, $mileage->value);
        $this->assertEquals(MileageUnit::KILOMETERS, $mileage->unit);
        $this->assertEquals('50000.50 km', $mileage->toString());
    }

    public function test_can_create_mileage_in_miles(): void
    {
        $mileage = Mileage::miles(31068.56);

        $this->assertEquals(31068.56, $mileage->value);
        $this->assertEquals(MileageUnit::MILES, $mileage->unit);
        $this->assertEquals('31068.56 mi', $mileage->toString());
    }

    public function test_can_convert_kilometers_to_miles(): void
    {
        $kmMileage = Mileage::kilometers(100.0);
        $miMileage = $kmMileage->convertTo(MileageUnit::MILES);

        $this->assertEqualsWithDelta(62.137, $miMileage->value, 0.001);
        $this->assertEquals(MileageUnit::MILES, $miMileage->unit);
    }

    public function test_can_convert_miles_to_kilometers(): void
    {
        $miMileage = Mileage::miles(62.137);
        $kmMileage = $miMileage->convertTo(MileageUnit::KILOMETERS);

        $this->assertEqualsWithDelta(100.0, $kmMileage->value, 0.001);
        $this->assertEquals(MileageUnit::KILOMETERS, $kmMileage->unit);
    }

    public function test_conversion_to_same_unit_returns_same_object(): void
    {
        $mileage = Mileage::kilometers(100.0);
        $converted = $mileage->convertTo(MileageUnit::KILOMETERS);

        $this->assertEquals($mileage, $converted);
    }

    public function test_can_get_kilometers_value(): void
    {
        $miMileage = Mileage::miles(62.137);

        $this->assertEqualsWithDelta(100.0, $miMileage->toKilometers(), 0.001);
    }

    public function test_can_get_miles_value(): void
    {
        $kmMileage = Mileage::kilometers(100.0);

        $this->assertEqualsWithDelta(62.137, $kmMileage->toMiles(), 0.001);
    }

    public function test_equality_comparison_works_correctly(): void
    {
        $km100 = Mileage::kilometers(100.0);
        $mi62137 = Mileage::miles(62.137);

        $this->assertTrue($km100->equals($mi62137));
    }

    public function test_json_serialization_contains_all_formats(): void
    {
        $mileage = Mileage::kilometers(100.0);
        $json = $mileage->jsonSerialize();

        $this->assertEquals(100.0, $json['value']);
        $this->assertEquals('km', $json['unit']);
        $this->assertEquals('100.00 km', $json['display']);
        $this->assertEquals(100.0, $json['kilometers']);
        $this->assertEqualsWithDelta(62.137, $json['miles'], 0.01);
    }

    public function test_can_create_from_array(): void
    {
        $data = ['value' => 50000.5, 'unit' => 'km'];
        $mileage = Mileage::fromArray($data);

        $this->assertEquals(50000.5, $mileage->value);
        $this->assertEquals(MileageUnit::KILOMETERS, $mileage->unit);
    }

    public function test_throws_exception_for_negative_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Mileage value cannot be negative');

        Mileage::kilometers(-100.0);
    }

    public function test_mileage_unit_enum_conversion_factor(): void
    {
        $this->assertEquals(1.0, MileageUnit::KILOMETERS->getConversionFactor());
        $this->assertEquals(1.609344, MileageUnit::MILES->getConversionFactor());
    }

    public function test_mileage_unit_enum_labels(): void
    {
        $this->assertEquals('Kilometers', MileageUnit::KILOMETERS->getLabel());
        $this->assertEquals('Miles', MileageUnit::MILES->getLabel());
    }

    public function test_mileage_unit_from_string(): void
    {
        $this->assertEquals(MileageUnit::KILOMETERS, MileageUnit::fromString('km'));
        $this->assertEquals(MileageUnit::KILOMETERS, MileageUnit::fromString('kilometers'));
        $this->assertEquals(MileageUnit::MILES, MileageUnit::fromString('mi'));
        $this->assertEquals(MileageUnit::MILES, MileageUnit::fromString('miles'));
    }

    public function test_mileage_unit_from_string_throws_exception_for_invalid_unit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid mileage unit: invalid');

        MileageUnit::fromString('invalid');
    }
}
