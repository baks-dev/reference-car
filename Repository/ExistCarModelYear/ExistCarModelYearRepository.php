<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelYear;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModelYear\CarModelYear;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;


final class ExistCarModelYearRepository implements ExistCarModelYearInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора model petrol
     */
    public function exist(CarModelYear|CarModelUid $carModel): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelYear::class, 'carModelYear')
            ->where('carModelYear.model = :model')
            ->setParameter(
                key: 'model',
                value: $carModel instanceof CarModel ? $carModel->getId() : $carModel,
                type: CarModelUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}