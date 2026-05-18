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

namespace BaksDev\Reference\Car\Repository\CarModelPetrolById;

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
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;

final readonly class CarModelPetrolByIdRepository implements CarModelPetrolByIdInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает детальную информацию о комплектации
     */
    public function find(CarModelPetrolUid $modelPetrol): CarModelPetrolByIdResult|false
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем id комплектаций
         */
        $dbal
            ->select('model_petrol.id')
            ->from(CarModelPetrol::class, 'model_petrol')
            ->where('model_petrol.id = :id')
            ->setParameter('id', $modelPetrol, CarModelPetrolUid::TYPE);


        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('model_petrol_name.value as name')
            ->join(
                'model_petrol',
                CarModelPetrolName::class,
                'model_petrol_name',
                'model_petrol_name.petrol = model_petrol.id',
            );


        /**
         * Получаем hp мощности
         */
        $dbal
            ->addSelect('model_petrol_hp.value as hp_value')
            ->leftJoin(
                'model_petrol',
                CarModelPetrolHP::class,
                'model_petrol_hp',
                'model_petrol_hp.petrol = model_petrol.id',
            );


        /**
         * Получаем kw мощности
         */
        $dbal
            ->addSelect('model_petrol_kw.value as kw_value')
            ->leftJoin(
                'model_petrol',
                CarModelPetrolKW::class,
                'model_petrol_kw',
                'model_petrol_kw.petrol = model_petrol.id',
            );


        /**
         * Получаем ps мощности
         */
        $dbal
            ->addSelect('model_petrol_ps.value as ps_value')
            ->leftJoin(
                'model_petrol',
                CarModelPetrolPS::class,
                'model_petrol_ps',
                'model_petrol_ps.petrol = model_petrol.id',
            );


        /**
         * Получаем id поколения (если есть)
         */
        $dbal
            ->addSelect('generation.id as generation_id')
            ->join(
                'model_petrol',
                CarModelGeneration::class,
                'generation',
                'generation.id = model_petrol.generation',
            );


        /**
         * Получаем название поколения (если есть)
         */
        $dbal
            ->addSelect('generation_name.value as generation_name')
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
            ->addSelect('model.id as model')
            ->join(
                'model_petrol',
                CarModel::class,
                'model',
                'model.id = generation.model',
            );


        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as model_name')
            ->join(
                'model_petrol',
                CarModelName::class,
                'model_name',
                'model_name.model = generation.model',
            );


        /**
         *  Получаем id бренда
         */
        $dbal
            ->addSelect('brand_name.brand')
            ->join(
                'model',
                CarBrand::class,
                'brand',
                'brand.id = model.brand',
            );


        /**
         * Получаем название бренда
         */
        $dbal
            ->addSelect('brand_name.value as brand_name')
            ->join(
                'brand',
                CarBrandName::class,
                'brand_name',
                'brand_name.brand = brand.id',
            );


        /**
         * Получаем года
         */
        $dbal
            ->addSelect("years.value AS years")
            ->join(
                'model_petrol',
                CarModelPetrolYear::class,
                'years',
                'years.petrol = model_petrol.id',
            );


        $dbal->allGroupByExclude();
        $dbal->orderBy('model_petrol_name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchHydrate(CarModelPetrolByIdResult::class);
    }
}