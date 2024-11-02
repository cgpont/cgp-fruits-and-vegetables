<?php

namespace App\Service;

use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;

class FoodService
{
    private StorageInterface $storage;
    private string $dataFilePath;

    public function __construct(StorageInterface $storage, string $dataFilePath)
    {
        $this->storage = $storage;
        $this->dataFilePath = $dataFilePath;
    }

    public function processJson(string $filePath): void
    {
        $data = json_decode(file_get_contents($filePath), true);
        $fruits = new FruitsCollection();
        $vegetables = new VegetablesCollection();

        foreach ($data as $item) {
            switch ($item['type']) {
                case 'fruit':
                    $fruits->add($item);
                    break;
                case 'vegetable':
                    $vegetables->add($item);
                    break;
                default:
                    $error = "Unkown food type";
                    break;
            }
        }

        $this->storage->save('fruits', $fruits);
        $this->storage->save('vegetables', $vegetables);
    }

    public function getItems(string $type, string $name = '', string $unit = 'g'): array
    {

        $this->processJson($this->dataFilePath);

        // Retrieve the collection based on type (e.g., 'fruits' or 'vegetables')
        $collection = $this->storage->get($type);
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

    private function convertItemsUnitToKgs (Array $items) {
        return array_map(function($item) {
            $item['quantity'] = $item['quantity'] / 1000;
            $item['unit'] = 'kg';
            return $item;
        }, $items);
    }

}
