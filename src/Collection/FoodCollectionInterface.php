<?php 

interface FoodCollectionInterface
{
    public function add(array $item): void;
    public function remove(int $id): void;
    public function list(): array;
    public function search(string $name, string $type): array;
}
