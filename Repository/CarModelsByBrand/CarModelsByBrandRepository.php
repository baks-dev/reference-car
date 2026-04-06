<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrand;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use InvalidArgumentException;

final class CarModelsByBrandRepository implements CarModelsByBrandIdInterface
{
    private CarBrandUid|false $brand = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод uid или сущность для дальнейшей передачи в запрос
     *
     * @param CarBrandUid|CarBrand $brand
     *
     * @return $this
     */
    public function forBrand(CarBrandUid|CarBrand $brand): self
    {
        if($brand instanceof CarBrand)
        {
            $brand = $brand->getId();
        }

        $this->brand = $brand;

        return $this;
    }

    /**
     * Метод возвращает все модели принадлежащие бренду
     *
     * @return PaginatorInterface
     */
    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        if(false === ($this->brand instanceof CarBrandUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarBrand');
        }

        /**
         * Получаем id модели
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model')
            ->where('model.brand = :brand')
            ->setParameter('brand', $this->brand, CarBrandUid::TYPE);

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

        return $this->paginator->fetchAllHydrate($dbal, CarModelsByBrandResult::class);
    }
}