<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is furnished
 *  to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in all
 *  copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 */

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelsByBrand;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use Generator;

final readonly class CarModelsByBrandRepository implements CarModelsByBrandInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все модели принадлежащие бренду
     * @return Generator<CarModelsByBrandResult>
     */
    public function findAll(CarBrandUid $brand): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем id модели
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model')
            ->where('model.brand = :brand')
            ->setParameter('brand', $brand, CarBrandUid::TYPE);


        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('name.value as name')
            ->addSelect('name.url')
            ->join('model', CarModelName::class, 'name', 'name.model = model.id');


        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('car_brand.id as brand_id')
            ->join(
                'model',
                CarBrand::class,
                'car_brand',
                'car_brand.id = model.brand',
            );


        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('car_brand_name.value as brand_name')
            ->addSelect('car_brand_name.url as brand_url')
            ->join(
                'car_brand',
                CarBrandName::class,
                'car_brand_name',
                'car_brand_name.brand = car_brand.id',
            );


        $dbal->orderBy('name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarModelsByBrandResult::class);
    }
}