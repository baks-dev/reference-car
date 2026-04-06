<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelYear;

use BaksDev\Reference\Car\Entity\CarModelYear\CarModelYear;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;

interface ExistCarModelYearInterface
{
    /**
     * Метод проверяет наличие идентификатора модели автомобиля
     */
    public function exist(CarModelYear|CarModelUid $carModel): bool;
}