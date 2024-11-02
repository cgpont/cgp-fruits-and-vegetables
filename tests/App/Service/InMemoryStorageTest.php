<?php

namespace App\Tests\Storage;

use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;
use App\Service\InMemoryStorage;
use PHPUnit\Framework\TestCase;

class InMemoryStorageTest extends TestCase
{
    public function testSaveAndRetrieveFruitsCollection(): void
    {
        // Initialize storage engine and collection
        $storage = new InMemoryStorage();
        $fruitsCollection = new FruitsCollection();
        
        // Add an item to the collection
        $item = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 500,
            'unit' => 'g'
        ];
        $fruitsCollection->add($item);
        
        // Save collection to storage engine
        $storage->save('fruits', $fruitsCollection);
        
        // Retrieve and verify the saved collection
        $retrievedCollection = $storage->get('fruits');
        $this->assertInstanceOf(FruitsCollection::class, $retrievedCollection);
        
        // Check the items within the collection
        $items = $retrievedCollection->list();
        $this->assertCount(1, $items);
        $this->assertEquals('Apple', $items[0]['name']);
        $this->assertEquals(500, $items[0]['quantity']);
    }

    public function testSaveAndRetrieveVegetablesCollection(): void
    {
        // Initialize storage engine and collection
        $storage = new InMemoryStorage();
        $vegetablesCollection = new VegetablesCollection();
        
        // Add an item to the collection
        $item = [
            'id' => 1,
            'name' => 'Carrot',
            'quantity' => 1,
            'unit' => 'kg'
        ];
        $vegetablesCollection->add($item);
        
        // Save collection to storage engine
        $storage->save('vegetables', $vegetablesCollection);
        
        // Retrieve and verify the saved collection
        $retrievedCollection = $storage->get('vegetables');
        $this->assertInstanceOf(VegetablesCollection::class, $retrievedCollection);
        
        // Check the items within the collection
        $items = $retrievedCollection->list();
        $this->assertCount(1, $items);
        $this->assertEquals('Carrot', $items[0]['name']);
        $this->assertEquals(1000, $items[0]['quantity']); // Ensure quantity normalized to grams
    }

    public function testRetrieveNonexistentCollection(): void
    {
        $storage = new InMemoryStorage();
        
        // Attempt to retrieve a non-existent collection
        $retrievedCollection = $storage->get('nonexistent');
        
        // Verify that null is returned when collection doesn't exist
        $this->assertNull($retrievedCollection);
    }

    public function testOverwriteCollection(): void
    {
        $storage = new InMemoryStorage();
        $fruitsCollection1 = new FruitsCollection();
        $fruitsCollection2 = new FruitsCollection();

        // Add an item to the first collection
        $item1 = [
            'id' => 1,
            'name' => 'Apple',
            'quantity' => 500,
            'unit' => 'g'
        ];
        $fruitsCollection1->add($item1);
        
        // Add a different item to the second collection
        $item2 = [
            'id' => 2,
            'name' => 'Banana',
            'quantity' => 1,
            'unit' => 'kg'
        ];
        $fruitsCollection2->add($item2);

        // Save the first collection, then overwrite it with the second collection
        $storage->save('fruits', $fruitsCollection1);
        $storage->save('fruits', $fruitsCollection2);

        // Retrieve and verify that only the second collection is stored
        $retrievedCollection = $storage->get('fruits');
        $this->assertCount(1, $retrievedCollection->list());
        $this->assertEquals('Banana', $retrievedCollection->list()[0]['name']);
        $this->assertEquals(1000, $retrievedCollection->list()[0]['quantity']); // Quantity in grams
    }
}
