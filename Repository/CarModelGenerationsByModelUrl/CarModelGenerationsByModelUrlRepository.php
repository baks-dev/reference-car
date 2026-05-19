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

namespace BaksDev\Reference\Car\Repository\CarModelGenerationsByModelUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use Doctrine\DBAL\Types\Types;
use Generator;

final readonly class CarModelGenerationsByModelUrlRepository implements CarModelGenerationsByModelUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все поколения, принадлежащие модели, по её url
     * @return Generator<CarModelGenerationsByModelUrlResult>
     */
    public function findAll(string $url): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем название модели и ее url
         */
        $dbal
            ->select('model_name.value as model_name')
            ->addSelect('model_name.url as model_url')
            ->from(CarModelName::class, 'model_name')
            ->where('model_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('model.id as model_id')
            ->join(
                'model_name',
                CarModel::class,
                'model',
                'model.id = model_name.model',
            );


        /**
         * Получаем id поколения
         */
        $dbal
            ->addSelect('generation.id')
            ->join(
                'model',
                CarModelGeneration::class,
                'generation',
                'generation.model = model.id',
            );


        /**
         * Получаем название поколения и его url
         */
        $dbal
            ->addSelect('generation_name.value as name')
            ->addSelect('generation_name.url')
            ->join(
                'generation',
                CarModelGenerationName::class,
                'generation_name',
                'generation_name.generation = generation.id',
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
                'generation',
                CarModelGenerationImage::class,
                'image',
                'image.generation = generation.id'
            );


        /**
         * Получаем id бренда
         */
        $dbal
            ->addSelect('brand.id as brand_id')
            ->join('model', CarBrand::class, 'brand', 'brand.id = model.brand');


        /**
         * Получаем название и url бренда
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


        $dbal->orderBy('generation_name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarModelGenerationsByModelUrlResult::class);
    }
}