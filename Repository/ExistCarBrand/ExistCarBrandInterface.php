<?php

namespace BaksDev\Reference\Car\Repository\ExistCarBrand;

use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;

interface ExistCarBrandInterface
{
    /**
     * Метод проверяет наличие идентификатора бренда автомобиля
     */
    public function exist(CarBrand|CarBrandUid $carBrand): bool;
}