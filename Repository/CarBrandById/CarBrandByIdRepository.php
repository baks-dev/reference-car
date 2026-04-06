<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarBrandById;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use InvalidArgumentException;

final class CarBrandByIdRepository implements CarBrandByIdInterface
{
    private CarBrandUid|false $brand = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о бренда
     */
    public function find(): CarBrandByIdResult|false
    {
        if(false === ($this->brand instanceof CarBrandUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarBrand');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id бренда
         */
        $dbal
            ->select('brand.id')
            ->from(CarBrand::class, 'brand')
            ->where('brand.id = :id')
            ->setParameter('id', $this->brand, CarBrandUid::TYPE);

        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('name.value as name')
            ->leftJoin(
                'brand',
                CarBrandName::class,
                'name',
                'name.brand = brand.id',
            );

        return $dbal->fetchHydrate(CarBrandByIdResult::class);
    }
}