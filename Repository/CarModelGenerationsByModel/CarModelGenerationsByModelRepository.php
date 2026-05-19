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

namespace BaksDev\Reference\Car\Repository\CarModelGenerationsByModel;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Generator;

final readonly class CarModelGenerationsByModelRepository implements CarModelGenerationsByModelInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все поколения принадлежащие модели
     * @return Generator<CarModelGenerationsByModelResult>
     */
    public function findAll(CarModelUid $model): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем id поколения
         */
        $dbal
            ->select('model_generation.id')
            ->from(CarModelGeneration::class, 'model_generation')
            ->where('model_generation.model = :model')
            ->setParameter('model', $model, CarModelUid::TYPE);


        /**
         * Получаем название поколения и его URL
         */
        $dbal
            ->addSelect('model_generation_name.value as name')
            ->addSelect('model_generation_name.url as url')
            ->join(
                'model_generation',
                CarModelGenerationName::class,
                'model_generation_name',
                'model_generation_name.generation = model_generation.id',
            );


        /**
         * Получаем изображение поколения
         */
        $dbal
            ->addSelect('image.ext as image_ext')
            ->addSelect('image.cdn as image_cdn')
            ->addSelect("
			    CASE
			       WHEN image.name IS NOT NULL 
			       THEN CONCAT ('/upload/".$dbal->table(CarModelGenerationImage::class)."' , '/', image.name)
			       ELSE NULL
			    END AS image_name
		    ")
            ->leftJoin(
                'model_generation',
                CarModelGenerationImage::class,
                'image',
                'image.generation = model_generation.id'
            );


        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('model_generation.model as model_id')
            ->join(
                'model_generation',
                CarModel::class,
                'model',
                'model.id = model_generation.model',
            );


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


        $dbal->orderBy('model_generation_name.value');

        return $dbal
             ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarModelGenerationsByModelResult::class);
    }
}