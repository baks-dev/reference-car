<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModel;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;


final class ExistCarModelRepository implements ExistCarModelInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора бренда автомобиля
     */
    public function exist(CarModel|CarModelUid $carModel): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModel::class, 'carModel')
            ->where('carModel.id = :id')
            ->setParameter(
                key: 'id',
                value: $carModel instanceof CarModel ? $carModel->getId() : $carModel,
                type: CarModelUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}