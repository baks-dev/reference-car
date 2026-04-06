<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarBrandByName;


use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;

final readonly class CarBrandByNameResult
{
    public function __construct(
        private string $id,
        private string $name
    ) {}

    public function getId(): CarBrandUid
    {
        return new CarBrandUid($this->id);
    }

    public function getName(): CarBrandName
    {
        return new CarBrandName($this->name);
    }

}