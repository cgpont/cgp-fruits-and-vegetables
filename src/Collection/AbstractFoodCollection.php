<?php

namespace App\Collection;

abstract class AbstractFoodCollection
{
    protected array $items = [];
    protected string $type;

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function add(array $item): void
    {
        // Ensure all quantities are stored in grams
        $item['quantity'] = $this->normalizeToGrams($item['quantity'], $item['unit']);
        $item['type'] = $this->type;
        $this->items[] = $item;
    }

    public function remove(int $id): void
    {
        // Remove an item by filtering out the matching ID
        $this->items = array_filter($this->items, fn($item) => $item['id'] !== $id);
    }

    public function list(): array
    {
        return $this->items;
    }

    public function search(string $name): array
    {
        // Search for items by name and type
        return array_filter($this->items, fn($item) => 
            stripos($item['name'], $name) !== false && $item['type'] === $this->type
        );
    }

    protected function normalizeToGrams(float $quantity, string $unit): float
    {
        return $unit === 'kg' ? $quantity * 1000 : $quantity;
    }
}
