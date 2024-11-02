<?php

namespace App\Tests\Service;

use App\Collection\AbstractFoodCollection;
use App\Service\FoodService;
use App\Service\StorageInterface;
use PHPUnit\Framework\TestCase;

class FoodServiceTest extends TestCase
{
    private $storage;
    private $foodService;

    protected function setUp(): void
    {
        // Mock the StorageInterface
        $this->storage = $this->createMock(StorageInterface::class);

        // Initialize FoodService with mocked storage and a sample file path
        $this->foodService = new FoodService($this->storage, __DIR__.'/test_request.json');
    }

    public function testGetItemsByType()
    {
        // Mock collection for fruits and expected data
        $fruitsCollection = $this->createMock(AbstractFoodCollection::class);
        $fruitsCollection->method('list')->willReturn([
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2000, 'unit' => 'g'],
            ['id' => 2, 'name' => 'Banana', 'quantity' => 1000, 'unit' => 'g']
        ]);

        // Mock storage to return fruits collection when 'fruits' type is requested
        $this->storage->method('get')->with('fruits')->willReturn($fruitsCollection);

        // Call getItems to retrieve fruits
        $result = $this->foodService->getItems('fruits');

        // Assert that the returned items match the expected data
        $this->assertCount(2, $result);
        $this->assertEquals('Apple', $result[0]['name']);
        $this->assertEquals(2000, $result[0]['quantity']);
    }

    public function testGetItemsByName()
    {
        // Mock collection for fruits
        $fruitsCollection = $this->createMock(AbstractFoodCollection::class);
        $fruitsCollection->method('search')->with('Apple')->willReturn([
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2000, 'unit' => 'g']
        ]);

        // Mock storage to return fruits collection when 'fruits' type is requested
        $this->storage->method('get')->with('fruits')->willReturn($fruitsCollection);

        // Call getItems to search for 'Apple' in the fruits collection
        $result = $this->foodService->getItems('fruits', 'Apple');

        // Assert that the returned items match the expected data
        $this->assertCount(1, $result);
        $this->assertEquals('Apple', $result[0]['name']);
        $this->assertEquals(2000, $result[0]['quantity']);
    }

    public function testGetItemsWithKilogramConversion()
    {
        // Mock collection for fruits
        $fruitsCollection = $this->createMock(AbstractFoodCollection::class);
        $fruitsCollection->method('list')->willReturn([
            ['id' => 1, 'name' => 'Apple', 'quantity' => 2000, 'unit' => 'g'],
            ['id' => 2, 'name' => 'Banana', 'quantity' => 1000, 'unit' => 'g']
        ]);

        // Mock storage to return fruits collection when 'fruits' type is requested
        $this->storage->method('get')->with('fruits')->willReturn($fruitsCollection);

        // Call getItems to retrieve items in kilograms
        $result = $this->foodService->getItems('fruits', '', 'kg');

        // Assert that quantities are converted to kilograms
        $this->assertEquals(2, $result[0]['quantity']);
        $this->assertEquals('kg', $result[0]['unit']);
        $this->assertEquals(1, $result[1]['quantity']);
        $this->assertEquals('kg', $result[1]['unit']);
    }

    public function testGetItemsEmptyCollection()
    {
        // Mock storage to return null if the collection type is not found
        $this->storage->method('get')->with('unknown')->willReturn(null);

        // Call getItems with an unknown collection type
        $result = $this->foodService->getItems('unknown');

        // Assert that an empty array is returned
        $this->assertEmpty($result);
    }
}
