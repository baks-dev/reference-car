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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Type\CarBrands\Id\Brands\CarBrandsInterface;
use BaksDev\Reference\Car\Type\CarBrands\Name\Brands\CarBrandsNameInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\CarModelGenerationsInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\ModelGenerations\CarModelGenerationsNameInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\ModelPetrols\CarModelPetrolPowerHPInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\CarModelPetrolInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\ModelPetrols\CarModelPetrolPowerKWInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\ModelPetrols\CarModelPetrolNameInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\ModelPetrols\CarModelPetrolPowerPSInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\ModelPetrols\CarModelPetrolSaleRegionInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\ModelPetrols\CarModelPetrolYearInterface;
use BaksDev\Reference\Car\Type\CarModels\Id\Models\CarModelsInterface;
use BaksDev\Reference\Car\Type\CarModels\Name\Models\CarModelsNameInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\ModelWheels\CarModelWheelsBackspacingInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\ModelWheels\CarModelWheelsBarInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\ModelWheels\CarModelWheelsDiameterInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\CarModelWheelsInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\ModelWheels\CarModelWheelsOffsetRangeInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\ModelWheels\CarModelWheelsProfileInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\ModelWheels\CarModelWheelsRimInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\ModelWheels\CarModelWheelsTireWeightInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\ModelWheels\CarModelWheelsWidthInterface;

return static function(ContainerConfigurator $container) {

    $services = $container->services()
        ->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    //    $services->alias(ContainerInterface::class, 'service_container');

    $services->load(BaksDevReferenceCarBundle::NAMESPACE, BaksDevReferenceCarBundle::PATH)
        ->exclude([
            BaksDevReferenceCarBundle::PATH.'{Entity,Resources,Type}',
            BaksDevReferenceCarBundle::PATH.'**'.DIRECTORY_SEPARATOR.'*Message.php',
            BaksDevReferenceCarBundle::PATH.'**'.DIRECTORY_SEPARATOR.'*Result.php',
            BaksDevReferenceCarBundle::PATH.'**'.DIRECTORY_SEPARATOR.'*DTO.php',
            BaksDevReferenceCarBundle::PATH.'**'.DIRECTORY_SEPARATOR.'*Test.php',
        ]);

    // /home/matlecks.baks.dev/vendor/baks-dev/reference-car/Type/Brand/Id/CarBrand

    /**
     * Загрузка Id Брендов
     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarBrands\Id\Brands\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarBrands', 'Id', 'Brands'])
    //    );

    $services->set(CarBrandsInterface::class)->class(CarBrandsInterface::class);


    /**
     * Загрузка Name Брендов
     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarBrands\Name\Brands\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarBrands', 'Name', 'Brands'])
    //    );

    $services->set(CarBrandsNameInterface::class)->class(CarBrandsNameInterface::class);


    //    /**
    //     * Загрузка Id моделей
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModels\Id\Models\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModels', 'Id', 'Models'])
    //    );

    $services->set(CarModelsInterface::class)->class(CarModelsInterface::class);


    //    /**
    //     * Загрузка Name моделей
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModels\Name\Models\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModels', 'Name', 'Models'])
    //    );


    $services->set(CarModelsNameInterface::class)->class(CarModelsNameInterface::class);

    //    /**
    //     * Загрузка Model Petrol
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\Id\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'Id', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolInterface::class)->class(CarModelPetrolInterface::class);

    //    /**
    //     * Загрузка Model Petrol Name
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\Name\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'Name', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolNameInterface::class)->class(CarModelPetrolNameInterface::class);

    //    /**
    //     * Загрузка Model Petrol Year
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\Year\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'Year', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolYearInterface::class)->class(CarModelPetrolYearInterface::class);

    //    /**
    //     * Загрузка Model Petrol SaleRegion
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\SaleRegion\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'SaleRegion', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolSaleRegionInterface::class)->class(CarModelPetrolSaleRegionInterface::class);

    //    /**
    //     * Загрузка Model Petrol HP
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\HP\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'HP', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolPowerHPInterface::class)->class(CarModelPetrolPowerHPInterface::class);

    //    /**
    //     * Загрузка Model Petrol KW
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\KW\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'KW', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolPowerKWInterface::class)->class(CarModelPetrolPowerKWInterface::class);

    //    /**
    //     * Загрузка Model Petrol PS
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelPetrols\PS\ModelPetrols\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelPetrols', 'PS', 'ModelPetrols'])
    //    );

    $services->set(CarModelPetrolPowerPSInterface::class)->class(CarModelPetrolPowerPSInterface::class);

    //    /**
    //     * Загрузка Model Generation
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelGenerations\Id\ModelGenerations\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelGenerations', 'Id', 'ModelGenerations'])
    //    );

    $services->set(CarModelGenerationsInterface::class)->class(CarModelGenerationsInterface::class);

    //    /**
    //     * Загрузка Model Generation Name
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelGenerations\Name\ModelGenerations\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelGenerations', 'Name', 'ModelGenerations'])
    //    );

    $services->set(CarModelGenerationsNameInterface::class)->class(CarModelGenerationsNameInterface::class);

    //    /**
    //     * Загрузка Model Wheel Backspacing
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Backspacing\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Backspacing', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsBackspacingInterface::class)->class(CarModelWheelsBackspacingInterface::class);

    //    /**
    //     * Загрузка Model Wheel Bar
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Bar\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Bar', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsBarInterface::class)->class(CarModelWheelsBarInterface::class);

    //    /**
    //     * Загрузка Model Wheel Diameter
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Diameter\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Diameter', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsDiameterInterface::class)->class(CarModelWheelsDiameterInterface::class);

    //    /**
    //     * Загрузка Model Wheel Id
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Id\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Id', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsInterface::class)->class(CarModelWheelsInterface::class);

    //    /**
    //     * Загрузка Model Wheel OffsetRange
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\OffsetRange\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'OffsetRange', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsOffsetRangeInterface::class)->class(CarModelWheelsOffsetRangeInterface::class);

    //    /**
    //     * Загрузка Model Wheel Profile
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Profile\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Profile', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsProfileInterface::class)->class(CarModelWheelsProfileInterface::class);

    //    /**
    //     * Загрузка Model Wheel Rim
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Rim\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Rim', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsRimInterface::class)->class(CarModelWheelsRimInterface::class);

    //    /**
    //     * Загрузка Model Wheel TireWeight
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\TireWeight\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'TireWeight', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsTireWeightInterface::class)->class(CarModelWheelsTireWeightInterface::class);

    //    /**
    //     * Загрузка Model Wheel Width
    //     */
    //    $services->load(
    //        BaksDevReferenceCarBundle::NAMESPACE.'Type\CarModelWheels\Width\ModelWheels\\',
    //        BaksDevReferenceCarBundle::PATH.implode(DIRECTORY_SEPARATOR, ['Type', 'CarModelWheels', 'Width', 'ModelWheels'])
    //    );

    $services->set(CarModelWheelsWidthInterface::class)->class(CarModelWheelsWidthInterface::class);

    //    foreach (glob(BaksDevReferenceCarBundle::PATH.'/Type/Brands/Collection/*.php') as $file) {
    //        $class = 'BaksDev\\Reference\\Car\\Type\\Brands\\Collection\\'.basename($file, '.php');
    //        $services->set($class)->tag('baks.car.brands');
    //    }
    //
    //    foreach (glob(BaksDevReferenceCarBundle::PATH.'/Type/Models/Collection/*.php') as $file) {
    //        $class = 'BaksDev\\Reference\\Car\\Type\\Models\\Collection\\'.basename($file, '.php');
    //        $services->set($class)->tag('baks.car.models');
    //    }
};
