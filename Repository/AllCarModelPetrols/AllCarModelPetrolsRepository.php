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

namespace BaksDev\Reference\Car\Repository\AllCarModelPetrols;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;

final class AllCarModelPetrolsRepository implements AllCarModelPetrolsInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /**
         * Получаем id комплектации
         */
        $dbal
            ->select('petrol.id')
            ->from(CarModelPetrol::class, 'petrol');

        /**
         * Получаем название поколения
         */
        $dbal
            ->addSelect('name.value as name')
            ->addSelect('name.url as url')
            ->join(
                'petrol',
                CarModelPetrolName::class,
                'name',
                'name.petrol = petrol.id',
            );


        /**
         * Получаем id поколения
         */
        $dbal
            ->join(
                'petrol',
                CarModelGeneration::class,
                'generation',
                'generation.id = petrol.generation',
            );


        /**
         * Получаем название поколения
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
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as model_name')
            ->addSelect('model_name.url as model_url')
            ->leftJoin(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            );


        /**
         * Получаем id бренда
         */
        $dbal->join('model', CarBrand::class, 'brand', 'brand.id = model.brand');


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


        /* Поиск */
        if($this->search?->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('name.value');
        }


        $dbal
            ->addOrderBy('brand_name.value')
            ->addOrderBy('model_name.value')
            ->addOrderBy('generation_name.value')
            ->addOrderBy('name.value');

        return $this->paginator->fetchAllHydrate($dbal, AllCarModelPetrolsResult::class);
    }
}