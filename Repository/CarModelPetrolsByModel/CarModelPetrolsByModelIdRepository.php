<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModel;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use InvalidArgumentException;

final class CarModelPetrolsByModelIdRepository implements CarModelPetrolsByModelIdInterface
{
    private CarModelUid|false $model = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод uid или сущность для дальнейшей передачи в запрос
     *
     * @param CarModelUid|CarModel $model
     *
     * @return $this
     */
    public function forModel(CarModelUid|CarModel $model): self
    {
        if($model instanceof CarModel)
        {
            $model = $model->getId();
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Метод возвращает все комплектации принадлежащие модели
     */
    public function findAll(): PaginatorInterface
    {
        if(false === ($this->model instanceof CarModelUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarModel');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id комплектации
         */
        $dbal
            ->select('modelPetrol.id')
            ->from(CarModelPetrol::class, 'modelPetrol')
            ->where('modelPetrol.model = :model')
            ->setParameter('model', $this->model, CarModelUid::TYPE);

        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('modelPetrolName.value as name')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolName::class,
                'modelPetrolName',
                'modelPetrolName.petrol = modelPetrol.id',
            );

        /**
         * Получаем id поколения
         */
        $dbal
            ->addSelect('modelPetrol.generation as generation_id')
            ->leftJoin(
                'modelPetrol',
                CarModelGeneration::class,
                'modelPetrolGeneration',
                'modelPetrolGeneration.id = modelPetrol.generation',
            );

        /**
         * Получаем название поколения
         */
        $dbal
            ->addSelect('modelPetrolGenerationName.value as generation_name')
            ->leftJoin(
                'modelPetrolGeneration',
                CarModelGenerationName::class,
                'modelPetrolGenerationName',
                'modelPetrolGenerationName.generation = modelPetrolGeneration.id',
            );

        /**
         * Получаем hp комплектации
         */
        $dbal
            ->addSelect('modelPetrolHP.value as hp')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolHP::class,
                'modelPetrolHP',
                'modelPetrolHP.petrol = modelPetrol.id',
            );

        return $this->paginator->fetchAllHydrate($dbal, CarModelPetrolsByModelIdResult::class);
    }
}