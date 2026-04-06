<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolId;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
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
use InvalidArgumentException;

final class CarModelWheelsByModelPetrolIdRepository implements CarModelWheelsByModelPetrolIdInterface
{
    private CarModelPetrolUid|false $modelPetrol = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод uid или сущность для дальнейшей передачи в запрос
     *
     * @param CarModelPetrolUid|CarModelPetrol $modelPetrol
     *
     * @return $this
     */
    public function forModelPetrol(CarModelPetrolUid|CarModelPetrol $modelPetrol): self
    {
        if($modelPetrol instanceof CarModelPetrol)
        {
            $modelPetrol = $modelPetrol->getId();
        }

        $this->modelPetrol = $modelPetrol;

        return $this;
    }

    /**
     * Метод возвращает все комплектации принадлежащие модели
     *
     * @return PaginatorInterface
     */
    public function findAll(): PaginatorInterface
    {
        if(false === ($this->modelPetrol instanceof CarModelPetrolUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarModelPetrol');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id колеса
         */
        $dbal
            ->select('car_model_wheel_table.id')
            ->from(CarModelWheel::class, 'car_model_wheel_table')
            ->where('car_model_wheel_table.petrol = :modelPetrol')
            ->setParameter('modelPetrol', $this->modelPetrol, CarModelPetrolUid::TYPE);

        /**
         * Получаем диаметр колеса
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
         * Получаем  профиль колеса
         *
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
         *
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
         *
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
         *
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
         *
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
         *
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
         *
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

        return $this->paginator->fetchAllAssociative($dbal);
    }
}