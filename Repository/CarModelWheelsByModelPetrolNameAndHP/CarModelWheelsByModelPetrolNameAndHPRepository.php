<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelWheelsByModelPetrolNameAndHP;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Core\Services\Paginator\PaginatorInterface;
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
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP as CarModelPetrolHPField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName as CarModelPetrolNameField;
use InvalidArgumentException;


final class CarModelWheelsByModelPetrolNameAndHPRepository implements CarModelWheelsByModelPetrolNameAndHPInterface
{
    private CarModelPetrolNameField|false $modelPetrolName = false;
    private CarModelPetrolHPField|false $modelPetrolHP = false;

    public function __construct(
        private DBALQueryBuilder $DBALQueryBuilder,
        private readonly PaginatorInterface $paginator,
    ) {}

    /**
     * Передаем в метод кастомный тип для поля названия для дальнейшей передачи в запрос
     *
     * @param CarModelPetrolNameField $modelPetrolName
     *
     * @return $this
     */
    public function forModelPetrolName(
        CarModelPetrolNameField $modelPetrolName,
    ): self
    {
        $this->modelPetrolName = $modelPetrolName;
        return $this;
    }

    /**
     * Передаем в метод кастомный тип для поля hp для дальнейшей передачи в запрос
     *
     * @param CarModelPetrolHPField $modelPetrolHP
     *
     * @return $this
     */
    public function forModelPetrolHP(
        CarModelPetrolHPField $modelPetrolHP
    ): self
    {
        $this->modelPetrolHP = $modelPetrolHP;
        return $this;
    }

    /**
     * Метод возвращает все колеса принадлежащие комплектации
     *
     * @return PaginatorInterface
     */
    public function findAll(): PaginatorInterface
    {
        if($this->modelPetrolName === null)
        {
            throw new InvalidArgumentException('Invalid Argument CarModelPetrolNameField');
        }

        if($this->modelPetrolHP === null)
        {
            throw new InvalidArgumentException('Invalid Argument CarModelPetrolHPField');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id комплектации
         */
        $dbal
            ->select('modelPetrol.id')
            ->from(CarModelPetrol::class, 'modelPetrol');

        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('modelPetrolName.value as car_model_petrol_name')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolName::class,
                'modelPetrolName',
                'modelPetrolName.petrol = modelPetrol.id',
            );

        $dbal
            ->addSelect('modelPetrolHP.value as car_model_petrol_hp')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolHP::class,
                'modelPetrolHP',
                'modelPetrolHP.petrol = modelPetrol.id',
            )
            ->where('LOWER(modelPetrolName.value) = LOWER(:modelPetrolName)')
            ->andWhere('LOWER(modelPetrolHP.value) = LOWER(:modelPetrolHP)')
            ->setParameter('modelPetrolName', $this->modelPetrolName)
            ->setParameter('modelPetrolHP', $this->modelPetrolHP);

        /**
         * Получаем id колес
         */
        $dbal
            ->addSelect('car_model_wheel_table.id')
            ->leftJoin(
                'modelPetrol',
                CarModelWheel::class,
                'car_model_wheel_table',
                'car_model_wheel_table.petrol = modelPetrol.id',
            );

        /**
         * Получаем диаметр колеса
         *
         * Wheel Diameter
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
         * Получаем профиль колес
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
         * Получаем ширину колес
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