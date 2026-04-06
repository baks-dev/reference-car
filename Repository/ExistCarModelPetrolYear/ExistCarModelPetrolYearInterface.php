<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolYear;

use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\ModelPetrols\CarModelPetrolYearInterface;

interface ExistCarModelPetrolYearInterface
{
    /**
     * Метод проверяет наличие наличие региона продаж
     */
    public function exist(
        CarModelPetrol|CarModelPetrolUid $carModelPetrol,
        CarModelPetrolYearInterface $carModelPetrolYear
    ): bool;
}