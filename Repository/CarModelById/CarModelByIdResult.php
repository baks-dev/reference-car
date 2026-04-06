<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelById;


use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;

final readonly class CarModelByIdResult
{

    public function __construct(
        private string $id,
        private string $brand,
        private string $brand_name,
        private string $name,
        private ?string $years,
        private ?string $regions
    ) {}

    public function getId(): CarModelUid
    {
        return new CarModelUid($this->id);
    }

    public function getBrand(): CarBrandUid
    {
        return new CarBrandUid($this->brand);
    }

    public function getBrand_name(): CarBrandName
    {
        return new CarBrandName($this->brand_name);
    }

    public function getName(): CarModelName
    {
        return new CarModelName($this->name);
    }

    public function getYears(): array|false
    {
        if(empty($this->years))
        {
            return false;
        }

        if(false == json_validate($this->years))
        {
            return false;
        }

        return json_decode($this->years, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getSaleRegions(): array|false
    {

        if(empty($this->regions))
        {
            return false;
        }

        if(false == json_validate($this->regions))
        {
            return false;
        }

        return json_decode($this->regions, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getRegions(): string|false
    {
        $regions = $this->getSaleRegions();

        return $regions ? implode(', ', $regions) : false;
    }


    public function getYearRange(): string
    {
        $minYear = $this->getYears() !== false ? current(min($this->getYears())) : '';
        $maxYear = $this->getYears() !== false ? current(max($this->getYears())) : '';

        if($minYear === $maxYear)
        {
            return $minYear;
        }

        return sprintf('%s - %s', $minYear, $maxYear);
    }

}