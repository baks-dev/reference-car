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

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\AllCarBrands;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Form\Search\SearchDTO;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImage;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use Generator;

final class AllCarBrandsRepository implements AllCarBrandsInterface
{
    private ?SearchDTO $search = null;

    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $Paginator
    ) {}

    public function search(SearchDTO $search): self
    {
        $this->search = $search;
        return $this;
    }

    private function builder(): DBALQueryBuilder
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();


        /**
         * Получаем id бренад
         */
        $dbal
            ->select('brand.id')
            ->from(CarBrand::class, 'brand');


        /**
         * Получаем название и url бренда
         */
        $dbal
            ->addSelect('name.value as name')
            ->addSelect('name.url')
            ->join(
                'brand',
                CarBrandName::class,
                'name',
                'name.brand = brand.id',
            );


        /**
         * Получаем изображение бренда
         */
        $dbal
            ->addSelect('image.ext as image_ext')
            ->addSelect('image.cdn as image_cdn')
            ->addSelect("
			    CASE
			       WHEN image.name IS NOT NULL 
			       THEN CONCAT ('/upload/".$dbal->table(CarBrandImage::class)."' , '/', image.name)
			       ELSE NULL
			    END AS image_name
		    ")
            ->leftJoin('brand', CarBrandImage::class, 'image', 'image.brand = brand.id');


        /* Поиск */
        if($this->search?->getQuery())
        {
            $dbal
                ->createSearchQueryBuilder($this->search)
                ->addSearchLike('name.value');
        }


        return $dbal;
    }


    /**
     * Метод получает все сохраненные в базе бренды
     * @return Generator<AllCarBrandsResult>
     */
    public function findAll(): Generator
    {
        $dbal = $this->builder();

        $dbal->orderBy('name.value');

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(AllCarBrandsResult::class);
    }


    /**
     * Метод получает все сохраненные в базе бренды в виде пагинатора
     * @return PaginatorInterface<AllCarBrandsResult>
     */
    public function findAllPaginator(): PaginatorInterface
    {
        $dbal = $this->builder();

        $dbal->orderBy('name.value');

        return $this->Paginator->fetchAllHydrate($dbal, AllCarBrandsResult::class);
    }
}
