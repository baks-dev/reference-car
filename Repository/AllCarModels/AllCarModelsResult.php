<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\AllCarModels;

use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;

final class AllCarModelsResult
{
    public function __construct(
        private readonly string $id,
        private readonly string $name,
    ) {}

    public function getId(): CarModelUid
    {
        return new CarModelUid($this->id);
    }

    public function getName(): CarModelName
    {
        return new CarModelName($this->name);
    }
}
