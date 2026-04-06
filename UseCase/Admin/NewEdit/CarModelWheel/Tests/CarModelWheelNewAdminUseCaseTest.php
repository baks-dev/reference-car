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

declare(strict_types=1);

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelWheel\Tests;

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
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\CarModelWheelBackspacing as CarModelWheelBackspacingField;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\CarModelWheelBar as CarModelWheelBarField;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter as CarModelWheelDiameterField;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\CarModelWheelOffsetRange as CarModelWheelOffsetRangeField;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\CarModelWheelProfile as CarModelWheelProfileField;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\CarModelWheelRim as CarModelWheelRimField;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\CarModelWheelTireWeight as CarModelWheelTireWeightField;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\CarModelWheelWidth as CarModelWheelWidthField;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelWheel\CarModelWheelDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelWheel\CarModelWheelHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

/**
 * @group reference-car
 * @group reference-car-usecase
 * @group reference-car-repository
 * @group reference-car-controller
 */
#[When(env: 'test')]
class CarModelWheelNewAdminUseCaseTest extends KernelTestCase
{
    /**
     * Удаляем тестовые данные перед началом тестов
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        self::clearTestData($em);
    }

    /**
     * Удаляем тестовые данные после завершения всех тестов
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $em */
        $em = self::getContainer()->get(EntityManagerInterface::class);

        self::clearTestData($em);
    }

    /**
     * Удаляет тестовые данные
     *
     * @param EntityManagerInterface $em
     * @return void
     */
    private static function clearTestData(EntityManagerInterface $em): void
    {
        $carModelPetrol = $em->getRepository(CarModelWheel::class)
            ->findOneBy(['id' => CarModelWheelUid::TEST]);

        if($carModelPetrol)
        {
            $em->remove($carModelPetrol);
        }

        $modelPetrolDiameter = $em->getRepository(CarModelWheelDiameter::class)
            ->findOneBy(['carModelWheel' => CarModelWheelUid::TEST]);

        if($modelPetrolDiameter)
        {
            $em->remove($modelPetrolDiameter);
        }

        $modelPetrolBar = $em->getRepository(CarModelWheelBar::class)
            ->findOneBy(['carModelWheel' => CarModelWheelUid::TEST]);

        if($modelPetrolBar)
        {
            $em->remove($modelPetrolBar);
        }

        $modelPetrolBackspacing = $em->getRepository(CarModelWheelBackspacing::class)
            ->findOneBy(['carModelWheel' => CarModelWheelUid::TEST]);

        if($modelPetrolBackspacing)
        {
            $em->remove($modelPetrolBackspacing);
        }

        $modelPetrolOffsetRange = $em->getRepository(CarModelWheelOffsetRange::class)
            ->findOneBy(['carModelWheel' => CarModelPetrolUid::TEST]);

        if($modelPetrolOffsetRange)
        {
            $em->remove($modelPetrolOffsetRange);
        }

        $modelPetrolProfile = $em->getRepository(CarModelWheelProfile::class)
            ->findOneBy(['carModelWheel' => CarModelPetrolUid::TEST]);

        if($modelPetrolProfile)
        {
            $em->remove($modelPetrolProfile);
        }

        $modelPetrolRim = $em->getRepository(CarModelWheelRim::class)
            ->findOneBy(['carModelWheel' => CarModelPetrolUid::TEST]);

        if($modelPetrolRim)
        {
            $em->remove($modelPetrolRim);
        }

        $modelPetrolTireWeight = $em->getRepository(CarModelWheelTireWeight::class)
            ->findOneBy(['carModelWheel' => CarModelPetrolUid::TEST]);

        if($modelPetrolTireWeight)
        {
            $em->remove($modelPetrolTireWeight);
        }

        $modelPetrolWidth = $em->getRepository(CarModelWheelWidth::class)
            ->findOneBy(['carModelWheel' => CarModelPetrolUid::TEST]);

        if($modelPetrolWidth)
        {
            $em->remove($modelPetrolWidth);
        }


        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        $carModelWheelHandler = self::getContainer()->get(CarModelWheelHandler::class);

        $carModelWheelDTO = new CarModelWheelDTO();
        $carModelWheelDTO->setId(new CarModelWheelUid(CarModelWheelUid::TEST));
        $carModelWheelDTO->setModelPetrol(new CarModelPetrolUid(CarModelPetrolUid::TEST));

        $carModelDiameterDTO = $carModelWheelDTO->getDiameter();
        $carModelDiameterDTO->setValue(new CarModelWheelDiameterField(CarModelWheelDiameterField::TEST));

        $carModelBarDTO = $carModelWheelDTO->getBar();
        $carModelBarDTO->setValue(new CarModelWheelBarField(CarModelWheelBarField::TEST));

        $carModelBackspacingDTO = $carModelWheelDTO->getBackspacing();
        $carModelBackspacingDTO->setValue(new CarModelWheelBackspacingField(CarModelWheelBackspacingField::TEST));

        $carModelOffsetRangeDTO = $carModelWheelDTO->getOffsetRange();
        $carModelOffsetRangeDTO->setValue(new CarModelWheelOffsetRangeField(CarModelWheelOffsetRangeField::TEST));

        $carModelProfileDTO = $carModelWheelDTO->getProfile();
        $carModelProfileDTO->setValue(new CarModelWheelProfileField(CarModelWheelProfileField::TEST));

        $carModelRimDTO = $carModelWheelDTO->getRim();
        $carModelRimDTO->setValue(new CarModelWheelRimField(CarModelWheelRimField::TEST));

        $carModelTireWeightDTO = $carModelWheelDTO->getTireWeight();
        $carModelTireWeightDTO->setValue(new CarModelWheelTireWeightField(CarModelWheelTireWeightField::TEST));

        $carModelWidthDTO = $carModelWheelDTO->getWidth();
        $carModelWidthDTO->setValue(new CarModelWheelWidthField(CarModelWheelWidthField::TEST));

        $carModelWheel = $carModelWheelHandler->handle($carModelWheelDTO);

        self::assertInstanceOf(CarModelWheel::class, $carModelWheel);
    }
}