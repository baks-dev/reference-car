<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarBrand;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;


final class ExistCarBrandRepository implements ExistCarBrandInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора бренда автомобиля
     */
    public function exist(CarBrand|CarBrandUid $carBrand): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarBrand::class, 'carBrand')
            ->where('carBrand.id = :id')
            ->setParameter(
                key: 'id',
                value: $carBrand instanceof CarBrand ? $carBrand->getId() : $carBrand,
                type: CarBrandUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}