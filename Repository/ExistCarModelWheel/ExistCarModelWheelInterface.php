<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelWheel;

use BaksDev\Reference\Car\Entity\CarModelWheel\CarModelWheel;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;

interface ExistCarModelWheelInterface
{
    /**
     * Метод проверяет наличие идентификатора модели автомобиля
     */
    public function exist(CarModelWheel|CarModelWheelUid $carModelWheel): bool;
}