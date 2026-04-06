<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\AllCarModelPetrols;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;


final class AllCarModelPetrolsResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $model_id,
        private readonly string $model_name,
    ) {}

    public function getId(): CarModelPetrolUid
    {
        return new CarModelPetrolUid($this->id);
    }

    public function getName(): CarModelPetrolName
    {
        return new CarModelPetrolName($this->name);
    }

    public function getModelId(): CarModelUid
    {
        return new CarModelUid($this->model_id);
    }

    public function getModelName(): CarModelName
    {
        return new CarModelName($this->model_name);
    }
}