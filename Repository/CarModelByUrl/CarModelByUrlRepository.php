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

namespace BaksDev\Reference\Car\Repository\CarModelByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use Doctrine\DBAL\Types\Types;

final readonly class CarModelByUrlRepository implements CarModelByUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает детальную информацию о модели
     */
    public function find(string $url): CarModelByUrlResult|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем имя модели
         */
        $dbal
            ->select('model_name.value as name')
            ->from(CarModelName::class, 'model_name')
            ->where('model_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем идентификатор модели
         */
        $dbal
            ->addSelect('model.id')
            ->join('model_name', CarModel::class, 'model', 'model.id = model_name.model');


        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('brand.id as brand_id')
            ->join('model', CarBrand::class, 'brand', 'brand.id = model.brand');


        /**
         * Получаем id, имя и url бренда
         */
        $dbal
            ->addSelect('brand_name.value as brand_name')
            ->addSelect('brand_name.url as brand_url')
            ->join(
                'brand',
                CarBrandName::class,
                'brand_name',
                'brand_name.brand = brand.id',
            );


        $dbal->allGroupByExclude();

        return $dbal->fetchHydrate(CarModelByUrlResult::class);
    }
}
