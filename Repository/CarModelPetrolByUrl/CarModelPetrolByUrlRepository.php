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

namespace BaksDev\Reference\Car\Repository\CarModelPetrolByUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use Doctrine\DBAL\Types\Types;

final readonly class CarModelPetrolByUrlRepository implements CarModelPetrolByUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}
    

    /**
     * Метод возвращает детальную информацию о комплектации
     */
    public function find(string $url): CarModelPetrolByUrlResult|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем название комплектации
         */
        $dbal
            ->select('petrol_name.value as name')
            ->from(CarModelPetrolName::class, 'petrol_name')
            ->where('petrol_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем id комплектации
         */
        $dbal
            ->addSelect('petrol.id')
            ->join(
                'petrol_name',
                CarModelPetrol::class,
                'petrol',
                'petrol.id = petrol_name.petrol'
            );


        /**
         * Получаем hp комплектации
         */
        $dbal
            ->addSelect('petrol_hp.value as hp_value')
            ->leftJoin(
                'petrol',
                CarModelPetrolHP::class,
                'petrol_hp',
                'petrol_hp.petrol = petrol.id',
            );


        /**
         * Получаем kw мощности
         */
        $dbal
            ->addSelect('petrol_kw.value as kw_value')
            ->leftJoin(
                'petrol',
                CarModelPetrolKW::class,
                'petrol_kw',
                'petrol_kw.petrol = petrol.id',
            );


        /**
         * Получаем ps мощности
         */
        $dbal
            ->addSelect('petrol_ps.value as ps_value')
            ->leftJoin(
                'petrol',
                CarModelPetrolPS::class,
                'petrol_ps',
                'petrol_ps.petrol = petrol.id',
            );


        /**
         * Получаем года
         */
        $dbal
            ->addSelect("petrol_years.value AS years")
            ->join(
                'petrol',
                CarModelPetrolYear::class,
                'petrol_years',
                'petrol_years.petrol = petrol.id',
            );


        /**
         * Получаем id поколения
         */
        $dbal
            ->addSelect('generation.id as generation_id')
            ->join(
                'petrol',
                CarModelGeneration::class,
                'generation',
                'generation.id = petrol.generation',
            );


        /**
         * Получаем название и url поколения
         */
        $dbal
            ->addSelect('generation_name.value as generation_name')
            ->addSelect('generation_name.url as generation_url')
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
            ->addSelect('generation_image.ext as generation_image_ext')
            ->addSelect('generation_image.cdn as generation_image_cdn')
            ->addSelect("
			    CASE
			       WHEN generation_image.name IS NOT NULL 
			       THEN CONCAT (
			            '/upload/".$dbal->table(CarModelGenerationImage::class)."',
			            '/',
			            generation_image.name
			       )
			       ELSE NULL
			    END AS generation_image_name
		    ")
            ->leftJoin(
                'generation',
                CarModelGenerationImage::class,
                'generation_image',
                'generation_image.generation = generation.id'
            );


        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('model.id as model_id')
            ->join(
                'generation',
                CarModel::class,
                'model',
                'model.id = generation.model',
            );


        /**
         * Получаем название и url модели
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
            ->join(
                'model',
                CarBrand::class,
                'brand',
                'brand.id = model.brand',
            );


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


        $dbal->allGroupByExclude();
        $dbal->orderBy('petrol_name.value');

        return $dbal->fetchHydrate(CarModelPetrolByUrlResult::class);
    }
}