<?php

namespace App\Service;

class FoodInputValidator
{
    private const VALID_TYPES = ['fruit', 'vegetable'];
    private const VALID_UNITS = ['g', 'kg'];

    /**
     * Validates item input data.
     *
     * @param array $data
     * @return array Array with 'valid' => bool and 'errors' => array
     */
    public function validateItemInput(array $data): array
    {
        $errors = [];

        // Check required fields
        if (empty($data['id']) || !is_int($data['id'])) {
            $errors[] = 'ID is required and must be an integer.';
        }

        if (empty($data['name']) || !is_string($data['name'])) {
            $errors[] = 'Name is required and must be a string.';
        }

        if (empty($data['type']) || !in_array($data['type'], self::VALID_TYPES)) {
            $errors[] = 'Type must be one of: ' . implode(', ', self::VALID_TYPES) . '.';
        }

        if (empty($data['quantity']) || !is_numeric($data['quantity']) || $data['quantity'] <= 0) {
            $errors[] = 'Quantity is required and must be a positive number.';
        }

        if (empty($data['unit']) || !in_array($data['unit'], self::VALID_UNITS)) {
            $errors[] = 'Unit must be one of: ' . implode(', ', self::VALID_UNITS) . '.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
