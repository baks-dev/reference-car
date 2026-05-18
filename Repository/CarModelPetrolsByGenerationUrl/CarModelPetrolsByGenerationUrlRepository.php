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

namespace BaksDev\Reference\Car\Repository\CarModelPetrolsByGenerationUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use Doctrine\DBAL\Types\Types;
use Generator;

final readonly class CarModelPetrolsByGenerationUrlRepository implements CarModelPetrolsByGenerationUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все комплектации, принадлежащие поколению, по его url
     * @return Generator<CarModelPetrolsByGenerationUrlResult>
     */
    public function findAll(string $url): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем название и url поколения
         */
        $dbal
            ->select('generation_name.value as generation_name')
            ->addSelect('generation_name.url as generation_url')
            ->from(CarModelGenerationName::class, 'generation_name')
            ->where('generation_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем id поколения
         */
        $dbal
            ->addSelect('generation.id as generation_id')
            ->join(
                'generation_name',
                CarModelGeneration::class,
                'generation',
                'generation.id = generation_name.generation',
            );


        /**
         * Получаем id комплектации
         */
        $dbal
            ->addSelect('petrol.id')
            ->join(
                'generation',
                CarModelPetrol::class,
                'petrol',
                'petrol.generation = generation.id',
            );


        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('petrol_name.value as name')
            ->addSelect('petrol_name.url as url')
            ->join(
                'petrol',
                CarModelPetrolName::class,
                'petrol_name',
                'petrol_name.petrol = petrol.id',
            );


        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('generation.model as model_id')
            ->join('generation', CarModel::class, 'model', 'model.id = generation.model');


        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as model_name')
            ->addSelect('model_name.url as model_url')
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
            ->addSelect('brand.id as brand_id')
            ->join('model', CarBrand::class, 'brand', 'brand.id = model.brand');


        /**
         * Получаем название бренда
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


        $dbal->orderBy('petrol_name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarModelPetrolsByGenerationUrlResult::class);
    }
}