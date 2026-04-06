<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByModelName;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName as CarModelNameField;
use InvalidArgumentException;

final class CarModelPetrolsByModelNameRepository implements CarModelPetrolsByModelNameInterface
{
    private CarModelNameField|false $modelName = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод кастомный тип для поля названия для дальнейшей передачи в запрос
     *
     * @param CarModelNameField $modelName
     *
     * @return $this
     */
    public function forModelName(CarModelNameField $modelName): self
    {
        $this->modelName = $modelName;

        return $this;
    }

    /**
     * Метод возвращает все комплектации принадлежащие модели по названию модели
     *
     * @return PaginatorInterface
     */
    public function findAll(): PaginatorInterface
    {
        if(false === ($this->modelName instanceof CarModelNameField))
        {
            throw new InvalidArgumentException('Invalid Argument CarModelNameField');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id комплектации
         */
        $dbal
            ->select('modelPetrol.id')
            ->from(CarModelPetrol::class, 'modelPetrol')
            ->leftJoin(
                'modelPetrol',
                CarModel::class,
                'model',
                'model.id = modelPetrol.model',
            );

        /**
         * Получаем id модели
         * Получаем название модели
         */
        $dbal
            ->addSelect('modelName.model as model_id')
            ->addSelect('modelName.value as model_name')
            ->leftJoin(
                'model',
                CarModelName::class,
                'modelName',
                'modelName.model = model.id',
            )
            ->where('LOWER(modelName.value) = LOWER(:model_name)')
            ->setParameter('model_name', $this->modelName);

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

        return $this->paginator->fetchAllHydrate($dbal, CarModelPetrolsByModelNameResult::class);
    }
}