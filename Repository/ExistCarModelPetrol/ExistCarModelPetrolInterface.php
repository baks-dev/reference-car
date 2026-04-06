<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrol;

use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;

interface ExistCarModelPetrolInterface
{
    /**
     * Метод проверяет наличие идентификатора модели автомобиля
     */
    public function exist(CarModelPetrol|CarModelPetrolUid $carModelPetrol): bool;
}