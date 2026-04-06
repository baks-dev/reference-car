<?php

namespace BaksDev\Reference\Car\Repository\AllCarModelPetrols;

use BaksDev\Core\Services\Paginator\PaginatorInterface;

interface AllCarModelPetrolsInterface
{
    public function findAll(): PaginatorInterface;
}