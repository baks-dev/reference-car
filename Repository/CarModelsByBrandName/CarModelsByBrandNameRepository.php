<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrandName;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName as CarBrandNameField;
use InvalidArgumentException;

final class CarModelsByBrandNameRepository implements CarModelsByBrandNameInterface
{
    private CarBrandNameField|false $carBrandName = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод кастомный тип для поля названия для дальнейшей передачи в запрос
     *
     * @param CarBrandNameField $carBrandName
     *
     * @return $this
     */
    public function forBrandName(CarBrandNameField $carBrandName): self
    {
        $this->carBrandName = $carBrandName;
        return $this;
    }

    /**
     * Метод возвращает все комплектации принадлежащие модели
     *
     * @return PaginatorInterface
     */
    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        if(false === ($this->carBrandName instanceof CarBrandNameField))
        {
            throw new InvalidArgumentException('Invalid Argument CarBrandName');
        }

        /**
         * Получаем id моделей
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model');

        /**
         * Получаем название моделей
         */
        $dbal
            ->addSelect('modelName.value as name')
            ->leftJoin(
                'model',
                CarModelName::class,
                'modelName',
                'modelName.model = model.id',
            );

        /**
         * Получаем id Бренда
         */
        $dbal
            ->addSelect('brand.id as brand_id')
            ->leftJoin(
                'model',
                CarBrand::class,
                'brand',
                'brand.id = model.brand',
            );

        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('brandName.value as brand_name')
            ->leftJoin(
                'brand',
                CarBrandName::class,
                'brandName',
                'brandName.brand = brand.id',
            )
            ->where('LOWER(brandName.value) = LOWER(:brandName)')
            ->setParameter('brandName', $this->carBrandName);

        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('carBrand.id as brand_id')
            ->leftJoin(
                'model',
                CarBrand::class,
                'carBrand',
                'carBrand.id = model.brand',
            );

        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('carBrandName.value as brand_name')
            ->leftJoin(
                'carBrand',
                CarBrandName::class,
                'carBrandName',
                'carBrandName.brand = carBrand.id',
            );

        return $this->paginator->fetchAllHydrate($dbal, CarModelsByBrandNameResult::class);
    }
}