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

namespace BaksDev\Reference\Car\Repository\CarModelsByBrandUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use Generator;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use Doctrine\DBAL\Types\Types;

final readonly class CarModelsByBrandUrlRepository implements CarModelsByBrandUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все модели, принадлежащие бренду
     * @return Generator<CarModelsByBrandUrlResult>
     */
    public function findAll(string $url): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем название и url бренда
         */
        $dbal
            ->select('brand_name.value as brand_name')
            ->addSelect('brand_name.url as brand_url')
            ->from(CarBrandName::class, 'brand_name')
            ->where('brand_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('brand.id as brand_id')
            ->join('brand_name', CarBrand::class, 'brand', 'brand.id = brand_name.brand');


        /**
         * Получаем id моделей
         */
        $dbal
            ->addSelect('model.id')
            ->join('brand', CarModel::class, 'model', 'model.brand = brand.id');


        /**
         * Получаем название моделей
         */
        $dbal
            ->addSelect('model_name.value as name')
            ->addSelect('model_name.url')
            ->join(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            );


        $dbal->orderBy('model_name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarModelsByBrandUrlResult::class);
    }
}