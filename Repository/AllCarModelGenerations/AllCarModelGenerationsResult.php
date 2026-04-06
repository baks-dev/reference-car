<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\AllCarModelGenerations;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;


final class AllCarModelGenerationsResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $model_id,
        private readonly string $model_name,
    ) {}

    public function getId(): CarModelGenerationUid
    {
        return new CarModelGenerationUid($this->id);
    }

    public function getName(): CarModelGenerationName
    {
        return new CarModelGenerationName($this->name);
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