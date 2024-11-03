<?php

namespace App\Tests\Service;

use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;
use App\Service\FoodService;
use App\Service\StorageInterface;
use PHPUnit\Framework\TestCase;

class FoodServiceTest extends TestCase
{
    private $storageMock;
    private $foodService;

    protected function setUp(): void
    {
        // Create a mock for the StorageInterface
        $this->storageMock = $this->createMock(StorageInterface::class);
        
        // Instantiate FoodService with the mocked storage
        $this->foodService = new FoodService($this->storageMock);
    }

    public function testGetItemsWithExistingTypeInGrams()
    {
        // Prepare test data for fruits collection
        $fruitsCollection = new FruitsCollection();
        $fruitsCollection->add([
            'id' => 1, 
            'name' => 'Apple', 
            'type' => 'fruit', 
            'quantity' => 500, 
            'unit' => 'g'
        ]);
        
        // Mock storage to return fruits collection
        $this->storageMock->expects($this->once())
            ->method('getAllData')
            ->willReturn(['fruits' => $fruitsCollection]);

        // Call getItems and verify results in grams
        $result = $this->foodService->getItems('fruits');
        $this->assertCount(1, $result);
        $this->assertEquals(500, $result[0]['quantity']);
        $this->assertEquals('g', $result[0]['unit']);
    }

    public function testGetItemsWithExistingTypeInKilograms()
    {
        // Prepare test data for fruits collection
        $fruitsCollection = new FruitsCollection();
        $fruitsCollection->add([
            'id' => 1, 
            'name' => 'Apple', 
            'type' => 'fruit', 
            'quantity' => 1000, 
            'unit' => 'g'
        ]);
        
        // Mock storage to return fruits collection
        $this->storageMock->expects($this->once())
            ->method('getAllData')
            ->willReturn(['fruits' => $fruitsCollection]);

        // Call getItems and verify conversion to kilograms
        $result = $this->foodService->getItems('fruits', '', 'kg');
        $this->assertCount(1, $result);
        $this->assertEquals(1, $result[0]['quantity']);  // Converted to kg
        $this->assertEquals('kg', $result[0]['unit']);
    }

    public function testGetItemsWithNonExistingType()
    {
        // Mock storage to return an empty array for any type
        $this->storageMock->expects($this->once())
            ->method('getAllData')
            ->willReturn([]);

        // Call getItems with a non-existent type
        $result = $this->foodService->getItems('non_existing_type');
        $this->assertEmpty($result);
    }

    public function testAddItemWithFruitType()
    {
        // Prepare an item to add and mock fruits collection
        $item = [
            'id' => 1,
            'name' => 'Apple',
            'type' => 'fruit',
            'quantity' => 500,
            'unit' => 'g'
        ];
        $fruitsCollection = new FruitsCollection();

        // Mock storage to return fruits collection and save it
        $this->storageMock->expects($this->once())
            ->method('get')
            ->with('fruits')
            ->willReturn($fruitsCollection);

        $this->storageMock->expects($this->once())
            ->method('save')
            ->with('fruits', $this->isInstanceOf(FruitsCollection::class));

        // Call addItem and verify that item is returned
        $result = $this->foodService->addItem($item);
        $this->assertEquals($item, $result);
    }

    public function testAddItemWithVegetableType()
    {
        // Prepare an item to add and mock vegetables collection
        $item = [
            'id' => 2,
            'name' => 'Carrot',
            'type' => 'vegetable',
            'quantity' => 200,
            'unit' => 'g'
        ];
        $vegetablesCollection = new VegetablesCollection();

        // Mock storage to return vegetables collection and save it
        $this->storageMock->expects($this->once())
            ->method('get')
            ->with('vegetables')
            ->willReturn($vegetablesCollection);

        $this->storageMock->expects($this->once())
            ->method('save')
            ->with('vegetables', $this->isInstanceOf(VegetablesCollection::class));

        // Call addItem and verify that item is returned
        $result = $this->foodService->addItem($item);
        $this->assertEquals($item, $result);
    }

    public function testAddItemWithUnknownType()
    {
        // Prepare an item with an unknown type
        $item = [
            'id' => 3,
            'name' => 'Unknown',
            'type' => 'unknown_type',
            'quantity' => 100,
            'unit' => 'g'
        ];

        // Expect an InvalidArgumentException to be thrown
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Unknown food type: unknown_type");

        // Call addItem with an unknown type
        $this->foodService->addItem($item);
    }
}
