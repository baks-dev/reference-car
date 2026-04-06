<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolByNameAndHP;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP as CarModelPetrolHPField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName as CarModelPetrolNameField;
use InvalidArgumentException;

final class CarModelPetrolByNameAndHPRepository implements CarModelPetrolByNameAndHPInterface
{
    private CarModelPetrolNameField|false $modelPetrolName = false;
    private CarModelPetrolHPField|false $modelPetrolHP = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о комплектации
     */
    public function find(): CarModelPetrolByNameAndHPResult|false
    {
        if(false === ($this->modelPetrolName instanceof CarModelPetrolNameField))
        {
            throw new InvalidArgumentException('Invalid Argument CarModelPetrolNameField');
        }

        if(false === ($this->modelPetrolHP instanceof CarModelPetrolHPField))
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
            ->addSelect('modelPetrolNameJoin.value as name')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolName::class,
                'modelPetrolNameJoin',
                'modelPetrolNameJoin.petrol = modelPetrol.id',
            )
            ->where('LOWER(modelPetrolNameJoin.value) = LOWER(:name)');

        /**
         * Получаем hp комплектации
         */
        $dbal
            ->addSelect('modelPetrolHPJoin.value as hp_value')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolHP::class,
                'modelPetrolHPJoin',
                'modelPetrolHPJoin.petrol = modelPetrol.id',
            )
            ->where('LOWER(modelPetrolHPJoin.value) = LOWER(:hp)')
            ->setParameter('name', $this->modelPetrolName)
            ->setParameter('hp', $this->modelPetrolHP);

        /**
         * Получаем id поколения (если есть)
         */
        $dbal
            ->addSelect('modelGeneration.id as generation_id')
            ->leftJoin(
                'modelPetrol',
                CarModelGeneration::class,
                'modelGeneration',
                'modelGeneration.id = modelPetrol.generation',
            );

        /**
         * Получаем название поколения (если есть)
         */
        $dbal
            ->addSelect('generationName.value as generation_name')
            ->leftJoin(
                'modelGeneration',
                CarModelGenerationName::class,
                'generationName',
                'generationName.generation = modelGeneration.id',
            );

        /**
         * Получаем kw мощности
         */
        $dbal
            ->addSelect('modelPetrolKW.value as kw_value')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolKW::class,
                'modelPetrolKW',
                'modelPetrolKW.petrol = modelPetrol.id',
            );

        /**
         * Получаем ps мощности
         */
        $dbal
            ->addSelect('modelPetrolPS.value as ps_value')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolPS::class,
                'modelPetrolPS',
                'modelPetrolPS.petrol = modelPetrol.id',
            );

        /**
         * Получаем название комплектации
         */
        $dbal
            ->addSelect('modelPetrolName.value as name')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolName::class,
                'modelPetrolName',
                'modelPetrolName.petrol = modelPetrol.id',
            );

        /**
         * Получаем id модели
         */
        $dbal
            ->addSelect('model.id as model_id')
            ->leftJoin(
                'modelPetrol',
                CarModel::class,
                'model',
                'model.id = modelPetrol.model',
            );

        /**
         * Получаем название модели
         */
        $dbal
            ->addSelect('model_name.value as model_name')
            ->leftJoin(
                'modelPetrol',
                CarModelName::class,
                'model_name',
                'model_name.model = modelPetrol.model',
            );

        /**
         * Получаем id бренда
         * Получаем название бренда
         */
        $dbal
            ->addSelect('brand_name.brand')
            ->addSelect('brand_name.value as brand_name')
            ->leftJoin(
                'model',
                CarBrandName::class,
                'brand_name',
                'brand_name.brand = model.brand',
            );

        /**
         * Получаем регионы продаж
         */
        $dbal
            ->addSelect(
                "JSON_AGG (DISTINCT saleRegion.value) AS sale_regions")
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolSaleRegion::class,
                'saleRegion',
                'saleRegion.petrol = modelPetrol.id',
            );

        /**
         * Получаем года
         */
        $dbal
            ->addSelect("JSON_AGG (DISTINCT years.value) AS years")
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolYear::class,
                'years',
                'years.petrol = modelPetrol.id',
            );

        $dbal->allGroupByExclude();

        return $dbal->fetchHydrate(CarModelPetrolByNameAndHPResult::class);
    }
}