<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\ExistCarModelWheel;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelWheel\CarModelWheel;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;


final class ExistCarModelWheelRepository implements ExistCarModelWheelInterface
{
    public function __construct(private readonly DBALQueryBuilder $DBALQueryBuilder) {}

    /**
     * Метод проверяет наличие идентификатора model petrol
     */
    public function exist(CarModelWheel|CarModelWheelUid $carModelWheel): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelWheel::class, 'carModelWheel')
            ->where('CarModelWheel.id = :carModelWheel')
            ->setParameter(
                key: 'carModelWheel',
                value: $carModelWheel instanceof CarModelWheel ? $carModelWheel->getId() : $carModelWheel,
                type: CarModelWheelUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}