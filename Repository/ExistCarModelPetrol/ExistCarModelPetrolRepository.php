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

namespace BaksDev\Reference\Car\Repository\ExistCarModelPetrol;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;

final readonly class ExistCarModelPetrolRepository implements ExistCarModelPetrolInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод проверяет наличие идентификатора model petrol
     */
    public function exist(CarModelPetrol|CarModelPetrolUid $carModelPetrol): bool
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        $dbal
            ->select()
            ->from(CarModelPetrol::class, 'carModelPetrol')
            ->where('carModelPetrol.id = :id')
            ->setParameter(
                key: 'id',
                value: $carModelPetrol instanceof CarModelPetrol ? $carModelPetrol->getId() : $carModelPetrol,
                type: CarModelPetrolUid::TYPE,
            );

        return $dbal->fetchExist();
    }
}