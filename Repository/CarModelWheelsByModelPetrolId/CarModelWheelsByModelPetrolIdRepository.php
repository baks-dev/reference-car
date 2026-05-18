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

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolId;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModelWheel\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Entity\CarModelWheel\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Entity\CarModelWheel\CarModelWheel;
use BaksDev\Reference\Car\Entity\CarModelWheel\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Entity\CarModelWheel\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Entity\CarModelWheel\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Entity\CarModelWheel\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Entity\CarModelWheel\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Entity\CarModelWheel\Width\CarModelWheelWidth;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use Generator;

final readonly class CarModelWheelsByModelPetrolIdRepository implements CarModelWheelsByModelPetrolIdInterface
{
    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}


    /**
     * Метод возвращает все комплектации принадлежащие модели
     * @return Generator<CarModelWheelsByModelPetrolIdResult>
     */
    public function findAll(CarModelPetrolUid $petrol): Generator
    {
        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);


        /**
         * Получаем id колеса
         */
        $dbal
            ->select('wheel.id')
            ->from(CarModelWheel::class, 'wheel')
            ->where('wheel.petrol = :petrol')
            ->setParameter('petrol', $petrol, CarModelPetrolUid::TYPE);


        /**
         * Получаем диаметр колеса
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
         * Получаем профиль колеса
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
         * Получаем ширину колеса
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
            ->join(
                'wheel',
                CarModelWheelRim::class,
                'rim',
                'wheel.id = rim.wheel',
            );


        /**
         * Получаем Диапазон смещения
         *
         * Wheel offset range
         */
        $dbal
            ->addSelect('car_model_wheel_offset_range_table.value as offset_range')
            ->join(
                'wheel',
                CarModelWheelOffsetRange::class,
                'car_model_wheel_offset_range_table',
                'wheel.id = car_model_wheel_offset_range_table.wheel',
            );


        /**
         * Получаем давление
         *
         * Wheel bar
         */
        $dbal
            ->addSelect('car_model_wheel_bar_table.value as bar')
            ->join(
                'wheel',
                CarModelWheelBar::class,
                'car_model_wheel_bar_table',
                'wheel.id = car_model_wheel_bar_table.wheel',
            );


        /**
         * Получаем вес
         *
         * Wheel tire weight
         */
        $dbal
            ->addSelect('car_model_wheel_tire_weight_table.value as tire_weight')
            ->join(
                'wheel',
                CarModelWheelTireWeight::class,
                'car_model_wheel_tire_weight_table',
                'wheel.id = car_model_wheel_tire_weight_table.wheel',
            );


        /**
         * Получаем Возврат
         *
         * Wheel backspacing
         */
        $dbal
            ->addSelect('car_model_wheel_backspacing_table.value as backspacing')
            ->join(
                'wheel',
                CarModelWheelBackspacing::class,
                'car_model_wheel_backspacing_table',
                'wheel.id = car_model_wheel_backspacing_table.wheel',
            );


        return $dbal->fetchAllHydrate(CarModelWheelsByModelPetrolIdResult::class);
    }
}