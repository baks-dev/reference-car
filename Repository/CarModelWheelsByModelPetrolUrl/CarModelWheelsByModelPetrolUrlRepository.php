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

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolUrl;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Entity\CarModelWheel\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Entity\CarModelWheel\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Entity\CarModelWheel\CarModelWheel;
use BaksDev\Reference\Car\Entity\CarModelWheel\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Entity\CarModelWheel\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Entity\CarModelWheel\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Entity\CarModelWheel\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Entity\CarModelWheel\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Entity\CarModelWheel\Width\CarModelWheelWidth;
use Doctrine\DBAL\Types\Types;
use Generator;

final readonly class CarModelWheelsByModelPetrolUrlRepository implements CarModelWheelsByModelPetrolUrlInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все колеса, принадлежащие комплектации
     * @return Generator<CarModelWheelsByModelPetrolUrlResult>
     */
    public function findAll(string $url): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем название комплектации
         */
        $dbal
            ->select('petrol_name.value as petrol_name')
            ->from(CarModelPetrolName::class, 'petrol_name')
            ->where('petrol_name.url = :url')
            ->setParameter('url', $url, Types::STRING);


        /**
         * Получаем id комплектации
         */
        $dbal
            ->addSelect('petrol.id as petrol_id')
            ->join(
                'petrol_name',
                CarModelPetrol::class,
                'petrol',
                'petrol.id = petrol_name.petrol'
            );


        /**
         * Получаем HP комплектации
         */
        $dbal
            ->addSelect('petrol_hp.value as petrol_hp')
            ->join(
                'petrol',
                CarModelPetrolHP::class,
                'petrol_hp',
                'petrol_hp.petrol = petrol.id',
            );


        /**
         * Получаем id колес
         */
        $dbal
            ->addSelect('wheel.id')
            ->join('petrol', CarModelWheel::class, 'wheel', 'wheel.petrol = petrol.id');


        /**
         * Получаем диаметр колеса
         *
         * Wheel Diameter
         */
        $dbal
            ->addSelect('diameter.value as diameter')
            ->join(
                'wheel',
                CarModelWheelDiameter::class,
                'diameter',
                'wheel.id = diameter.wheel',
            );


        /**
         * Получаем профиль колес
         *
         * Wheel Profile
         */
        $dbal
            ->addSelect('profile.value as profile')
            ->join(
                'wheel',
                CarModelWheelProfile::class,
                'profile',
                'wheel.id = profile.wheel',
            );


        /**
         * Получаем ширину колес
         *
         * Wheel width
         */
        $dbal
            ->addSelect('width.value as width')
            ->join(
                'wheel',
                CarModelWheelWidth::class,
                'width',
                'wheel.id = width.wheel',
            );


        /**
         * Получаем обод колеса
         *
         * Wheel rim
         */
        $dbal
            ->addSelect('rim.value as rim')
            ->join('wheel', CarModelWheelRim::class, 'rim', 'wheel.id = rim.wheel');


        /**
         * Получаем Диапазон смещения
         *
         * Wheel offset range
         */
        $dbal
            ->addSelect('offset_range.value as offset_range')
            ->leftJoin(
                'wheel',
                CarModelWheelOffsetRange::class,
                'offset_range',
                'wheel.id = offset_range.wheel',
            );


        /**
         * Получаем давление
         *
         * Wheel bar
         */
        $dbal
            ->addSelect('bar.value as bar')
            ->leftJoin('wheel', CarModelWheelBar::class, 'bar', 'wheel.id = bar.wheel');


        /**
         * Получаем вес
         *
         * Wheel tire weight
         */
        $dbal
            ->addSelect('tire_weight.value as tire_weight')
            ->join(
                'wheel',
                CarModelWheelTireWeight::class,
                'tire_weight',
                'wheel.id = tire_weight.wheel',
            );


        /**
         * Получаем Возврат
         *
         * Wheel backspacing
         */
        $dbal
            ->addSelect('backspacing.value as backspacing')
            ->leftJoin(
                'wheel',
                CarModelWheelBackspacing::class,
                'backspacing',
                'wheel.id = backspacing.wheel',
            );


        return $dbal->fetchAllHydrate(CarModelWheelsByModelPetrolUrlResult::class);
    }
}