<?php

namespace BaksDev\Reference\Car\Repository\ExistCarModelGeneration;

use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;

interface ExistCarModelGenerationInterface
{
    /**
     * Метод проверяет наличие идентификатора модели автомобиля
     */
    public function exist(CarModelGeneration|CarModelGenerationUid $carModelGeneration): bool;
}