<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModel;

use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;

interface ExistCarModelInterface
{
    /**
     * Метод проверяет наличие идентификатора модели автомобиля
     */
    public function exist(CarModel|CarModelUid $carModel): bool;
}