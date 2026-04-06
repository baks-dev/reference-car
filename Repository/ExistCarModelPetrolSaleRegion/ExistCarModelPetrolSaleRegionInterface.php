<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolSaleRegion;

use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\ModelPetrols\CarModelPetrolSaleRegionInterface;

interface ExistCarModelPetrolSaleRegionInterface
{
    /**
     * Метод проверяет наличие наличие региона продаж
     */
    public function exist(
        CarModelPetrol|CarModelPetrolUid $carModelPetrol,
        CarModelPetrolSaleRegionInterface $carModelPetrolSaleRegion
    ): bool;
}