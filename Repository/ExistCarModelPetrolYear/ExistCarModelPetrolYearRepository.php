<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolYear;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear as YearField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\ModelPetrols\CarModelPetrolYearInterface;


final class ExistCarModelPetrolYearRepository implements ExistCarModelPetrolYearInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие наличие года
     */
    public function exist(
        CarModelPetrol|CarModelPetrolUid $carModelPetrol,
        CarModelPetrolYearInterface $carModelPetrolYear
    ): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelPetrolYear::class, 'carModelPetrolYear')
            ->where('carModelPetrolYear.model_petrol = :id')
            ->where('carModelPetrolYear.value = :value')
            ->setParameter(
                key: 'id',
                value: $carModelPetrol instanceof CarModelPetrol ? $carModelPetrol->getId() : $carModelPetrol,
                type: CarModelPetrolUid::TYPE,
            )
            ->setParameter(
                key: 'value',
                value: $carModelPetrolYear instanceof CarModelPetrolYearInterface
                    ? $carModelPetrolYear::getValue() : $carModelPetrolYear::getValue(),
                type: YearField::TYPE,
            );

        return $dbal->fetchExist();
    }
}