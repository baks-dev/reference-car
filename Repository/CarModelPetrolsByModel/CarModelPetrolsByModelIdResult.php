<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModel;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;

final class CarModelPetrolsByModelIdResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
        private readonly string $generation_id,
        private readonly string $generation_name,
        private readonly string $hp,
    ) {}

    public function getId(): CarModelWheelUid
    {
        return new CarModelWheelUid($this->id);
    }

    public function getStringId(): string
    {
        return $this->id;
    }

    public function getName(): CarModelPetrolName
    {
        return new CarModelPetrolName($this->name);
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
