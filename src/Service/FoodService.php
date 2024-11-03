<?php

namespace App\Service;

use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;
use App\Service\StorageInterface;

class FoodService
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getItems(string $type, string $name = '', string $unit = 'g'): array
    {
        // Retrieve the collection based on type (e.g., 'fruits' or 'vegetables')
        $collections = $this->storage->getAllData();        
        $collection = $collections[$type] ?? null;
        if (!$collection) {
            return [];
        }

        // Search the collection by name if provided
        $items = $name ? $collection->search($name) : $collection->list();

        // Convert quantities to requested unit if necessary
        if ($unit === 'kg') {
            $items = $this->convertItemsUnitToKgs($items);
        }

        return $items;
    }

    private function convertItemsUnitToKgs(array $items): array
    {
        return array_map(function($item) {
            $item['quantity'] = $item['quantity'] / 1000;
            $item['unit'] = 'kg';
            return $item;
        }, $items);
    }

    public function addItem(array $item): array
    {
        switch ($item['type']) {
            case 'fruit':
                $collection = $this->storage->get('fruits') ?? new FruitsCollection();
                $collection->add($item);
                $this->storage->save('fruits', $collection);
                break;

            case 'vegetable':
                $collection = $this->storage->get('vegetables') ?? new VegetablesCollection();
                $collection->add($item);
                $this->storage->save('vegetables', $collection);
                break;

            default:
                throw new \InvalidArgumentException("Unknown food type: " . $item['type']);
        }

        return $item;
    }
}
