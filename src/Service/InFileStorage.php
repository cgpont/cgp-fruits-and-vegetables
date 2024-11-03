<?php

namespace App\Service;

use App\Collection\AbstractFoodCollection;
use App\Collection\FruitsCollection;
use App\Collection\VegetablesCollection;

class InFileStorage implements StorageInterface
{
    private string $dataFilePath;
    private string $dataStoragePath;
    private array $data = [];

    public function __construct(string $dataFilePath, string $dataStoragePath)
    {
        $this->dataFilePath = $dataFilePath;
        $this->dataStoragePath = $dataStoragePath;
        $this->loadData();
    }

    /**
     * Load raw data from the JSON file.
     */
    private function loadData(): void
    {
        if (file_exists($this->dataStoragePath)) {
            $storedData = json_decode(file_get_contents( $this->dataStoragePath), true) ?? [];
            $this->saveStoredDataAsCollections($storedData);
        } else {
            if (file_exists($this->dataFilePath)) {
                $originalData = json_decode(file_get_contents( $this->dataFilePath), true) ?? [];
                $this->saveInitialDataAsCollections($originalData);
            }
        }
        

    }

    /**
     * Save original data as collections.
     */
    private function saveInitialDataAsCollections(array $originalData): void
    {
        $this->data['fruits'] = new FruitsCollection();
        $this->data['vegetables'] = new VegetablesCollection();

        foreach ($originalData as $item) {
            switch ($item['type']) {
                case 'fruit':
                    $this->data['fruits']->add($item);
                    break;
                case 'vegetable':
                    $this->data['vegetables']->add($item);
                    break;
                default:
                    throw new \InvalidArgumentException("Unknown food type: " . $item['type']);
                    break;                    
            }
        }
    }

    /**
     * Save stored data as collections.
     */
    private function saveStoredDataAsCollections(array $storedData): void
    {
        $this->data['fruits'] = new FruitsCollection();
        $this->data['vegetables'] = new VegetablesCollection();

        foreach ($storedData['fruits'] as $fruit) {  
            $this->data['fruits']->add($fruit);
        }

        foreach ($storedData['vegetables'] as $vegetable) {  
            $this->data['vegetables']->add($vegetable);
        }
        
    }

    public function getAllData(): ?array
    {       
        return $this->data;
    }    

    public function save(string $type, AbstractFoodCollection $collection): void
    {
        $dataPreparedForFile = [];
        $this->data[$type] = $collection;
        foreach ($this->data as $type => $foodCollection) {
            $dataPreparedForFile[$type] = $foodCollection->list();
        }        
        file_put_contents($this->dataStoragePath, json_encode($dataPreparedForFile, JSON_PRETTY_PRINT));
    }

    public function get(string $type): ?AbstractFoodCollection 
    {       
        return $this->data[$type] ?? null;
    }
}
