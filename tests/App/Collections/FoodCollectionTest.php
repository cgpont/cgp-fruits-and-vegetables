<?php

namespace App\Tests\Collection;

use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;
use PHPUnit\Framework\TestCase;

class FoodCollectionTest extends TestCase
{
    public function testAddItemToFruitsCollection(): void
    {
        $fruitsCollection = new FruitsCollection();
        $item = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 2,
            'unit' => 'kg'
        ];
        
        $fruitsCollection->add($item);
        $items = $fruitsCollection->list();
        
        $this->assertCount(1, $items);
        $this->assertEquals(2000, $items[0]['quantity']); // Assert quantity in grams
        $this->assertEquals('fruit', $items[0]['type']);  // Assert type is fruit
    }

    public function testAddItemToVegetablesCollection(): void
    {
        $vegetablesCollection = new VegetablesCollection();
        $item = [
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 500,
            'unit' => 'g'
        ];
        
        $vegetablesCollection->add($item);
        $items = $vegetablesCollection->list();
        
        $this->assertCount(1, $items);
        $this->assertEquals(500, $items[0]['quantity']); // Assert quantity in grams
        $this->assertEquals('vegetable', $items[0]['type']);  // Assert type is vegetable
    }

    public function testRemoveItemFromCollection(): void
    {
        $fruitsCollection = new FruitsCollection();
        $item = [
            'id' => 1,
            'name' => 'Banana',
            'quantity' => 100,
            'unit' => 'g'
        ];
        
        $fruitsCollection->add($item);
        $this->assertCount(1, $fruitsCollection->list());
        
        $fruitsCollection->remove(1);
        $this->assertCount(0, $fruitsCollection->list());
    }

    public function testListItems(): void
    {
        $vegetablesCollection = new VegetablesCollection();
        
        $item1 = ['id' => 1, 'name' => 'Carrot', 'quantity' => 500, 'unit' => 'g'];
        $item2 = ['id' => 2, 'name' => 'Tomato', 'quantity' => 1, 'unit' => 'kg'];
        
        $vegetablesCollection->add($item1);
        $vegetablesCollection->add($item2);
        
        $items = $vegetablesCollection->list();
        
        $this->assertCount(2, $items);
        $this->assertEquals(1000, $items[1]['quantity']); // Tomato quantity in grams
        $this->assertEquals('vegetable', $items[1]['type']);
    }

    public function testSearchItemByName(): void
    {
        $fruitsCollection = new FruitsCollection();
        
        $item1 = ['id' => 2, 'name' => 'Banana', 'quantity' => 1, 'unit' => 'kg'];
        $item2 = ['id' => 1, 'name' => 'Apple', 'quantity' => 500, 'unit' => 'g'];
                
        $fruitsCollection->add($item1);
        $fruitsCollection->add($item2);
        
        $searchResults = $fruitsCollection->search('Banana');
        
        $this->assertCount(1, $searchResults);
        $this->assertEquals('Banana', $searchResults[0]['name']);
        $this->assertEquals(1000, $searchResults[0]['quantity']); // Quantity in grams
        $this->assertEquals('fruit', $searchResults[0]['type']);
    }
}
