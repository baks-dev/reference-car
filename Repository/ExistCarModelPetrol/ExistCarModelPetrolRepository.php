<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrol;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;


final class ExistCarModelPetrolRepository implements ExistCarModelPetrolInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора model petrol
     */
    public function exist(CarModelPetrol|CarModelPetrolUid $carModelPetrol): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelPetrol::class, 'carModelPetrol')
            ->where('carModelPetrol.id = :id')
            ->setParameter(
                key: 'id',
                value: $carModelPetrol instanceof CarModelPetrol ? $carModelPetrol->getId() : $carModelPetrol,
                type: CarModelPetrolUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}