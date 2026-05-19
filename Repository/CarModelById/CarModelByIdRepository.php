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

namespace BaksDev\Reference\Car\Repository\CarModelById;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;

final readonly class CarModelByIdRepository implements CarModelByIdInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает детальную информацию о модели
     */
    public function find(CarModelUid $model): CarModelByIdResult|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Основной запрос для получения данных модели
         */
        $dbal
            ->select('model.id')
            ->from(CarModel::class, 'model')
            ->where('model.id = :id')
            ->setParameter('id', $model, CarModelUid::TYPE);


        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as name')
            ->join(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            );


        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('brand_name.brand')
            ->join('model', CarBrand::class, 'brand', 'brand.id = model.brand');


        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('brand_name.value as brand_name')
            ->join(
                'model',
                CarBrandName::class,
                'brand_name',
                'brand_name.brand = model.brand',
            );


        $dbal->allGroupByExclude();

        return $dbal->fetchHydrate(CarModelByIdResult::class);

    }
}
