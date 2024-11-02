<?php

namespace App\Service;

use App\Collection\AbstractFoodCollection;

class InMemoryStorage implements StorageInterface
{
    private array $data = [];

    public function save(string $type, AbstractFoodCollection $collection): void
    {
        $this->data[$type] = $collection;
    }

    public function get(string $type): ?AbstractFoodCollection
    {
        return $this->data[$type] ?? null;
    }
}
