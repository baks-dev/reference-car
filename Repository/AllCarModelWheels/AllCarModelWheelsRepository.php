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

namespace BaksDev\Reference\Car\Repository\AllCarModelWheels;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
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

final class AllCarModelWheelsRepository implements AllCarModelWheelsInterface
{
    public function __construct(
        private readonly DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    public function findAll(): PaginatorInterface
    {
        $dbal = $this->DBALQueryBuilder
            ->createQueryBuilder(self::class)
            ->bindLocal();

        /**
         * Получаем id колеса
         */
        $dbal
            ->select(
                'car_model_wheel_table.id',
            )
            ->from(CarModelWheel::class, 'car_model_wheel_table');

        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('car_model_petrol_name_table.value as petrol_name')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelPetrolName::class,
                'car_model_petrol_name_table',
                'car_model_wheel_table.petrol = car_model_petrol_name_table.petrol',
            );

        /**
         * Получаем диаметр колеса
         * Wheel diameter
         */
        $dbal
            ->addSelect('car_model_wheel_diameter_table.value as wheel_diameter')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelDiameter::class,
                'car_model_wheel_diameter_table',
                'car_model_wheel_table.id = car_model_wheel_diameter_table.wheel',
            );

        /**
         * Получаем профиль колеса
         * Wheel Profile
         */
        $dbal
            ->addSelect('car_model_wheel_profile_table.value as wheel_profile')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelProfile::class,
                'car_model_wheel_profile_table',
                'car_model_wheel_table.id = car_model_wheel_profile_table.wheel',
            );

        /**
         * Получаем ширину колеса
         * Wheel width
         */
        $dbal
            ->addSelect('car_model_wheel_width_table.value as wheel_width')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelWidth::class,
                'car_model_wheel_width_table',
                'car_model_wheel_table.id = car_model_wheel_width_table.wheel',
            );

        /**
         * Получаем обод колеса
         * Wheel rim
         */
        $dbal
            ->addSelect('car_model_wheel_rim_table.value as wheel_rim')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelRim::class,
                'car_model_wheel_rim_table',
                'car_model_wheel_table.id = car_model_wheel_rim_table.wheel',
            );

        /**
         * Получаем Диапазон смещения
         * Wheel offset range
         */
        $dbal
            ->addSelect('car_model_wheel_offset_range_table.value as wheel_offset_range')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelOffsetRange::class,
                'car_model_wheel_offset_range_table',
                'car_model_wheel_table.id = car_model_wheel_offset_range_table.wheel',
            );

        /**
         * Получаем давление
         * Wheel bar
         */
        $dbal
            ->addSelect('car_model_wheel_bar_table.value as wheel_bar')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelBar::class,
                'car_model_wheel_bar_table',
                'car_model_wheel_table.id = car_model_wheel_bar_table.wheel',
            );

        /**
         * Получаем вес
         * Wheel tire weight
         */
        $dbal
            ->addSelect('car_model_wheel_tire_weight_table.value as wheel_tire_weight')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelTireWeight::class,
                'car_model_wheel_tire_weight_table',
                'car_model_wheel_table.id = car_model_wheel_tire_weight_table.wheel',
            );

        /**
         * Получаем Возврат
         * Wheel backspacing
         */
        $dbal
            ->addSelect('car_model_wheel_backspacing_table.value as wheel_backspacing')
            ->leftJoin(
                'car_model_wheel_table',
                CarModelWheelBackspacing::class,
                'car_model_wheel_backspacing_table',
                'car_model_wheel_table.id = car_model_wheel_backspacing_table.wheel',
            );

        $dbal->orderBy('car_model_wheel_diameter_table.value');

        return $this->paginator->fetchAllHydrate($dbal, AllCarModelWheelsResult::class);
    }
}
