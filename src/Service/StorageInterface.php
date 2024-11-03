<?php

namespace App\Service;

use App\Collection\AbstractFoodCollection;

interface StorageInterface
{
    public function getAllData(): ?array;
    public function save(string $type, AbstractFoodCollection $collection): void;
    public function get(string $type): ?AbstractFoodCollection;
}
