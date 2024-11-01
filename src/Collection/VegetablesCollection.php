<?php

namespace App\Collection;

class VegetablesCollection extends AbstractFoodCollection
{
    public function __construct()
    {
        parent::__construct('vegetable');
    }
}