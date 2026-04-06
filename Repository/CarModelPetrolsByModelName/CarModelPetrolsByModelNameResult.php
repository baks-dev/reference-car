<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModelName;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;

final class CarModelPetrolsByModelNameResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $model_id,
        private readonly string $model_name,
        private readonly string $name,
        private readonly string $generation_id,
        private readonly string $generation_name,
        private readonly string $hp,
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

    public function getGenerationId(): CarModelGenerationUid
    {
        return new CarModelGenerationUid($this->generation_id);
    }

    public function getGenerationName(): CarModelGenerationName
    {
        return new CarModelGenerationName($this->generation_name);
    }

    public function getHp(): CarModelPetrolHP
    {
        return new CarModelPetrolHP($this->hp);
    }
}
