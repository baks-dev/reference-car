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

namespace BaksDev\Reference\Car\Repository\CarBrandImageLocal;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImage;
use Generator;

final readonly class CarBrandImageLocalRepository implements CarBrandImageLocalInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все файлы изображений, расположенных локально (CDN = FALSE)
     * @return Generator<CarBrandImageLocalResult>
     */
    public function findAll(): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select('image.name as image_name')
            ->from(CarBrandImage::class, 'image')
            ->where('image.cdn IS NOT TRUE');

        $dbal
            ->addSelect('image.brand as brand_id')
            ->join(
                'image',
                CarBrand::class,
                'brand',
                'brand.id = image.brand'
            );

        return $dbal
            ->enableCache('reference-car', 3600)
            ->fetchAllHydrate(CarBrandImageLocalResult::class);
    }
}