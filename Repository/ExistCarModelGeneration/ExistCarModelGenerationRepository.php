<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelGeneration;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;


final class ExistCarModelGenerationRepository implements ExistCarModelGenerationInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора model petrol
     */
    public function exist(CarModelGeneration|CarModelGenerationUid $carModelGeneration): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelGeneration::class, 'carModelGeneration')
            ->where('carModelGeneration.id = :id')
            ->setParameter(
                key: 'id',
                value: $carModelGeneration instanceof CarModelGeneration ? $carModelGeneration->getId() : $carModelGeneration,
                type: CarModelGenerationUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}