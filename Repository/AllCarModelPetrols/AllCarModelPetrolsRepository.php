<?php

namespace BaksDev\Reference\Car\Repository\AllCarModelPetrols;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;

final class AllCarModelPetrolsRepository implements AllCarModelPetrolsInterface
{
    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator
    ) {}

    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /**
         * Получаем id комплектации
         */
        $dbal
            ->select('petrol.id')
            ->from(CarModelPetrol::class, 'petrol');

        /**
         * Получаем название поколения
         */
        $dbal
            ->addSelect('name.value as name')
            ->leftJoin(
                'petrol',
                CarModelPetrolName::class,
                'name',
                'name.petrol = petrol.id',
            );

        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('model.id as model_id')
            ->leftJoin(
                'petrol',
                CarModel::class,
                'model',
                'model.id = petrol.model',
            );

        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as model_name')
            ->leftJoin(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            );

        $dbal->orderBy('name.value');

        return $this->paginator->fetchAllHydrate($dbal, AllCarModelPetrolsResult::class);
    }
}