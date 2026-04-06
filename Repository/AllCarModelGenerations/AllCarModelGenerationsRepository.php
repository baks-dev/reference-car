<?php
/*
 *  Copyright 2025.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Repository\AllCarModelGenerations;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;


final class AllCarModelGenerationsRepository implements AllCarModelGenerationsInterface
{

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator
    ) {}

    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /**
         * Получаем id поколения
         */
        $dbal
            ->select('generation.id')
            ->from(
                CarModelGeneration::class,
                'generation');

        /**
         * Получаем имя поколения
         */
        $dbal
            ->addSelect('generation_name.value as name')
            ->leftJoin(
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
            ->leftJoin(
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
            ->leftJoin(
                'model',
                CarModelName::class,
                'model_name',
                'model_name.model = model.id',
            );

        $dbal->orderBy('model_name.value')->addOrderBy('generation_name.value');

        return $this->paginator->fetchAllHydrate($dbal, AllCarModelGenerationsResult::class);
    }
}
