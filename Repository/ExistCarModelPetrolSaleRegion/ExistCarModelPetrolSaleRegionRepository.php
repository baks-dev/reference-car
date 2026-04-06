<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrolSaleRegion;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\CarModelPetrolSaleRegion as SaleRegionField;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\ModelPetrols\CarModelPetrolSaleRegionInterface;


final class ExistCarModelPetrolSaleRegionRepository implements ExistCarModelPetrolSaleRegionInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие наличие региона продаж
     */
    public function exist(
        CarModelPetrol|CarModelPetrolUid $carModelPetrol,
        CarModelPetrolSaleRegionInterface $carModelPetrolSaleRegion
    ): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelPetrolSaleRegion::class, 'carModelPetrolSaleRegion')
            ->where('carModelPetrolSaleRegion.model_petrol = :id')
            ->where('carModelPetrolSaleRegion.value = :value')
            ->setParameter(
                key: 'id',
                value: $carModelPetrol instanceof CarModelPetrol ? $carModelPetrol->getId() : $carModelPetrol,
                type: CarModelPetrolUid::TYPE,
            )
            ->setParameter(
                key: 'value',
                value: $carModelPetrolSaleRegion instanceof CarModelPetrolSaleRegionInterface
                    ? $carModelPetrolSaleRegion::getValue() : $carModelPetrolSaleRegion::getValue(),
                type: SaleRegionField::TYPE,
            );

        return $dbal->fetchExist();
    }
}