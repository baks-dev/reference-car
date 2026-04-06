<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarBrandByName;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName as CarBrandNameField;
use InvalidArgumentException;

final class CarBrandByNameRepository implements CarBrandByNameInterface
{
    private CarBrandNameField|false $carBrandName = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о бренде
     */
    public function find(): CarBrandByNameResult|false
    {
        if(false === ($this->carBrandName instanceof CarBrandNameField))
        {
            throw new InvalidArgumentException('Invalid Argument CarBrandName');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id бренда
         */
        $dbal
            ->select('brand.id')
            ->from(CarBrand::class, 'brand');

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
            )
            ->where('LOWER(name.value) = LOWER(:name)')
            ->setParameter('name', $this->carBrandName);

        return $dbal->fetchHydrate(CarBrandByNameResult::class);
    }
}