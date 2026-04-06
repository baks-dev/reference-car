<?php

declare(strict_types=1);

namespace BaksDev\Reference\Car\Repository\CarModelPetrolById;

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
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use InvalidArgumentException;

final class CarModelPetrolByIdRepository implements CarModelPetrolByIdInterface
{
    private CarModelPetrolUid|false $modelPetrol = false;

    public function __construct(private DBALQueryBuilder $DBALQueryBuilder) {}

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
     * Метод возвращает детальную информацию о комплектации
     */
    public function find(): CarModelPetrolByIdResult|false
    {
        if(false === ($this->modelPetrol instanceof CarModelPetrolUid))
        {
            throw new InvalidArgumentException('Invalid Argument CarModelPetrol');
        }

        $dbal = $this->DBALQueryBuilder->createQueryBuilder(self::class);

        /**
         * Получаем id комплектаций
         */
        $dbal
            ->select('modelPetrol.id')
            ->from(CarModelPetrol::class, 'modelPetrol')
            ->where('modelPetrol.id = :id')
            ->setParameter('id', $this->modelPetrol, CarModelPetrolUid::TYPE);

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
         * Получаем hp мощности
         */
        $dbal
            ->addSelect('modelPetrolHP.value as hp_value')
            ->leftJoin(
                'modelPetrol',
                CarModelPetrolHP::class,
                'modelPetrolHP',
                'modelPetrolHP.petrol = modelPetrol.id',
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
            ->addSelect('model.id as model')
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

        return $dbal->fetchHydrate(CarModelPetrolByIdResult::class);
    }
}