<?php

namespace Src\Domain\Repositories;

use Src\Domain\Entities\Product;

interface ProductRepository
{
    public function getById(string $id): ?Product;
}