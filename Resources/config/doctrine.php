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
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandType;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandNameType;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationType;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationNameType;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHPType;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolType;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKWType;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolNameType;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPSType;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\CarModelPetrolSaleRegionType;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYearType;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelType;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelNameType;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing;
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacingType;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBarType;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameterType;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelType;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRangeType;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfileType;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRimType;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeightType;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidthType;
use Symfony\Config\DoctrineConfig;

return static function(ContainerConfigurator $container, DoctrineConfig $doctrine) {

    $doctrine->dbal()->type(CarBrandUid::TYPE)->class(CarBrandType::class);
    $doctrine->dbal()->type(CarBrandName::TYPE)->class(CarBrandNameType::class);
    $doctrine->dbal()->type(CarModelUid::TYPE)->class(CarModelType::class);
    $doctrine->dbal()->type(CarModelName::TYPE)->class(CarModelNameType::class);
    $doctrine->dbal()->type(CarModelPetrolUid::TYPE)->class(CarModelPetrolType::class);
    $doctrine->dbal()->type(CarModelPetrolName::TYPE)->class(CarModelPetrolNameType::class);
    $doctrine->dbal()->type(CarModelPetrolHP::TYPE)->class(CarModelPetrolHPType::class);
    $doctrine->dbal()->type(CarModelPetrolKW::TYPE)->class(CarModelPetrolKWType::class);
    $doctrine->dbal()->type(CarModelPetrolPS::TYPE)->class(CarModelPetrolPSType::class);
    $doctrine->dbal()->type(CarModelPetrolSaleRegion::TYPE)->class(CarModelPetrolSaleRegionType::class);
    $doctrine->dbal()->type(CarModelPetrolYear::TYPE)->class(CarModelPetrolYearType::class);
    $doctrine->dbal()->type(CarModelGenerationUid::TYPE)->class(CarModelGenerationType::class);
    $doctrine->dbal()->type(CarModelGenerationName::TYPE)->class(CarModelGenerationNameType::class);
    $doctrine->dbal()->type(CarModelWheelUid::TYPE)->class(CarModelWheelType::class);
    $doctrine->dbal()->type(CarModelWheelDiameter::TYPE)->class(CarModelWheelDiameterType::class);
    $doctrine->dbal()->type(CarModelWheelWidth::TYPE)->class(CarModelWheelWidthType::class);
    $doctrine->dbal()->type(CarModelWheelProfile::TYPE)->class(CarModelWheelProfileType::class);
    $doctrine->dbal()->type(CarModelWheelBackspacing::TYPE)->class(CarModelWheelBackspacingType::class);
    $doctrine->dbal()->type(CarModelWheelBar::TYPE)->class(CarModelWheelBarType::class);
    $doctrine->dbal()->type(CarModelWheelOffsetRange::TYPE)->class(CarModelWheelOffsetRangeType::class);
    $doctrine->dbal()->type(CarModelWheelRim::TYPE)->class(CarModelWheelRimType::class);
    $doctrine->dbal()->type(CarModelWheelTireWeight::TYPE)->class(CarModelWheelTireWeightType::class);

    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    /** Value Resolver */

    $services->set(CarBrandUid::class)->class(CarBrandUid::class);
    $services->set(CarBrandName::class)->class(CarBrandName::class);
    $services->set(CarModelUid::class)->class(CarModelUid::class);
    $services->set(CarModelName::class)->class(CarModelName::class);
    $services->set(CarModelPetrolUid::class)->class(CarModelPetrolUid::class);
    $services->set(CarModelPetrolName::class)->class(CarModelPetrolName::class);
    $services->set(CarModelPetrolHP::class)->class(CarModelPetrolHP::class);
    $services->set(CarModelPetrolKW::class)->class(CarModelPetrolKW::class);
    $services->set(CarModelPetrolSaleRegion::class)->class(CarModelPetrolSaleRegion::class);
    $services->set(CarModelPetrolYear::class)->class(CarModelPetrolYear::class);
    $services->set(CarModelGenerationUid::class)->class(CarModelGenerationUid::class);
    $services->set(CarModelGenerationName::class)->class(CarModelGenerationName::class);
    $services->set(CarModelWheelUid::class)->class(CarModelWheelUid::class);
    $services->set(CarModelWheelDiameter::class)->class(CarModelWheelDiameter::class);
    $services->set(CarModelWheelWidth::class)->class(CarModelWheelWidth::class);
    $services->set(CarModelWheelProfile::class)->class(CarModelWheelProfile::class);
    $services->set(CarModelWheelBackspacing::class)->class(CarModelWheelBackspacing::class);
    $services->set(CarModelWheelBar::class)->class(CarModelWheelBar::class);
    $services->set(CarModelWheelOffsetRange::class)->class(CarModelWheelOffsetRange::class);
    $services->set(CarModelWheelRim::class)->class(CarModelWheelRim::class);
    $services->set(CarModelWheelTireWeight::class)->class(CarModelWheelTireWeight::class);

    $emDefault = $doctrine->orm()->entityManager('default')->autoMapping(true);

    $emDefault
        ->mapping('reference-car')
        ->type('attribute')
        ->dir(BaksDevReferenceCarBundle::PATH.'Entity')
        ->isBundle(false)
        ->prefix(BaksDevReferenceCarBundle::NAMESPACE.'\\Entity')
        ->alias('reference-car');
};
