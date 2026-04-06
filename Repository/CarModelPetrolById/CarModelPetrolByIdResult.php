<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolById;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;

final readonly class CarModelPetrolByIdResult
{
    public function __construct(
        private string $id,
        private string $model,
        private ?string $generation_id,
        private ?string $generation_name,
        private ?string $hp_value,
        private ?string $kw_value,
        private ?string $ps_value,
        private string $name,
        private string $model_name,
        private string $brand,
        private string $brand_name,
        private string $sale_regions,
        private string $years
    ) {}

    public function getId(): CarModelPetrolUid
    {
        return new CarModelPetrolUid($this->id);
    }

    public function getModel(): CarModelUid
    {
        return new CarModelUid($this->model);
    }

    public function getGenerationId(): CarModelGenerationUid
    {
        return new CarModelGenerationUid($this->generation_id);
    }

    public function getGenerationName(): ?string
    {
        return $this->generation_name;
    }

    public function getHp_value(): ?string
    {
        return $this->hp_value;
    }

    public function getKw_value(): ?string
    {
        return $this->kw_value;
    }

    public function getPs_value(): ?string
    {
        return $this->ps_value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getModelName(): string
    {
        return $this->model_name;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getBrandName(): string
    {
        return $this->brand_name;
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

        if(empty($this->sale_regions))
        {
            return false;
        }

        if(false == json_validate($this->sale_regions))
        {
            return false;
        }

        return json_decode($this->sale_regions, true, 512, JSON_THROW_ON_ERROR);
    }

    public function getRegions(): string|false
    {
        $regions = $this->getSaleRegions();

        return $regions ? implode(', ', $regions) : false;
    }

    public function getYearRange(): string
    {
        $minYear = min($this->getYears());
        $maxYear = max($this->getYears());

        if($minYear === $maxYear)
        {
            return $minYear;
        }

        return sprintf('%s - %s', $minYear, $maxYear);
    }
}