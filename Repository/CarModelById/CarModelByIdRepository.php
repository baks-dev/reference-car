<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelById;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use InvalidArgumentException;

final class CarModelByIdRepository implements CarModelByIdInterface
{
    private CarModelUid|false $model = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о модели
     */
    public function find(): CarModelByIdResult|false
    {
        if(false === ($this->model instanceof CarModelUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarModel');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Основной запрос для получения данных модели
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model')
            ->where('model.id = :id')
            ->setParameter('id', $this->model, CarModelUid::TYPE);

        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('name.value as name')
            ->leftJoin(
                'model',
                CarModelName::class,
                'name',
                'name.model = model.id',
            );

        /**
         * Получаем id бренда
         * Получаем название бренда
         */
        $dbal
            ->addSelect('brand_name.brand')
            ->addSelect('brand_name.value as brand_name')
            ->leftJoin(
                'model',
                CarBrandName::class,
                'brand_name',
                'brand_name.brand = model.brand',
            );

        /**
         * Join`м комплектации для получения всех годов и регионов продаж
         */
        $dbal
            ->leftJoin(
                'model',
                CarModelPetrol::class,
                'petrol',
                'petrol.model = model.id',
            );

        /**
         * Получаем все года всех комплектаций модели
         */
        $dbal->addSelect(
            "
            JSON_AGG 
                (DISTINCT
                    JSONB_BUILD_OBJECT
                    (
                        'year', petrol_year.value
                    )
                ) FILTER (WHERE petrol_year.value IS NOT NULL) AS years")
            ->leftJoin(
                'petrol',
                CarModelPetrolYear::class,
                'petrol_year',
                'petrol_year.petrol = petrol.id',
            );

        /**
         * Получаем все регионы продаж всех комплектаций модели
         */
        $dbal->addSelect(
            "JSON_AGG (DISTINCT region.value) AS regions")
            ->leftJoin(
                'petrol',
                CarModelPetrolSaleRegion::class,
                'region',
                'region.petrol = petrol.id',
            );

        $dbal->allGroupByExclude();

        return $dbal->fetchHydrate(CarModelByIdResult::class);

    }

}
