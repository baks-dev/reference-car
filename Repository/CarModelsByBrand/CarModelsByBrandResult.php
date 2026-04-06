<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrand;

use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;

final class CarModelsByBrandResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $brand_id,
        private readonly string $brand_name,
    ) {}

    public function getId(): CarBrandUid
    {
        return new CarBrandUid($this->id);
    }

    public function getStringId(): string
    {
        return $this->id;
    }

    public function getName(): CarBrandName
    {
        return new CarBrandName($this->name);
    }

    public function getStringName(): string
    {
        return $this->name;
    }

    public function getBrandId(): CarBrandUid
    {
        return new CarBrandUid($this->brand_id);
    }

    public function getBrandName(): CarBrandName
    {
        return new CarBrandName($this->brand_name);
    }
}
