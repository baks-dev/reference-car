<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelByName;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName as CarModelNameField;
use InvalidArgumentException;

final class CarModelByNameRepository implements CarModelByNameInterface
{
    private CarModelNameField|false $modelName = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о модели
     */
    public function find(): CarModelByNameResult|false
    {
        if(false === ($this->modelName instanceof CarModelNameField))
        {
            throw new InvalidArgumentException('Invalid Argument CarModelNameField');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Основной запрос для получения данных модели
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model');

        $dbal
            ->addSelect('model_name.value as name')
            ->leftJoin(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            )
            ->where('LOWER(model_name.value) = LOWER(:model_name)')
            ->setParameter('model_name', $this->modelName);

        /**
         * Получаем id бренда
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
         * Получаем комплектации модели для получения всех годов и регионов продаж
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

        return $dbal->fetchHydrate(CarModelByNameResult::class);
    }
}
