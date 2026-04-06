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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\Tests;

use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Entity\CarModelPetrol\HP\CarModelPetrolHP;
use BaksDev\Reference\Car\Entity\CarModelPetrol\KW\CarModelPetrolKW;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Name\CarModelPetrolName;
use BaksDev\Reference\Car\Entity\CarModelPetrol\PS\CarModelPetrolPS;
use BaksDev\Reference\Car\Entity\CarModelPetrol\SaleRegion\CarModelPetrolSaleRegion;
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP as CarModelPetrolHPField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW as CarModelPetrolKWField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName as CarModelPetrolNameField;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS as CarModelPetrolPSField;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\CarModelPetrolSaleRegion as CarModelPetrolSaleRegionField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear as CarModelPetrolYearField;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolSaleRegion\CarModelPetrolSaleRegionDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolYear\CarModelPetrolYearDTO;
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
class CarModelPetrolNewAdminUseCaseTest extends KernelTestCase
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
        $carModelPetrol = $em->getRepository(CarModelPetrol::class)
            ->findOneBy(['id' => CarModelPetrolUid::TEST]);

        if($carModelPetrol)
        {
            $em->remove($carModelPetrol);
        }

        $modelPetrolName = $em->getRepository(CarModelPetrolName::class)
            ->findOneBy(['modelPetrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolName)
        {
            $em->remove($modelPetrolName);
        }

        $modelPetrolHP = $em->getRepository(CarModelPetrolHP::class)
            ->findOneBy(['modelPetrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolHP)
        {
            $em->remove($modelPetrolHP);
        }

        $modelPetrolKW = $em->getRepository(CarModelPetrolKW::class)
            ->findOneBy(['modelPetrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolKW)
        {
            $em->remove($modelPetrolKW);
        }

        $modelPetrolPS = $em->getRepository(CarModelPetrolPS::class)
            ->findOneBy(['modelPetrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolPS)
        {
            $em->remove($modelPetrolPS);
        }

        $modelPetrolYears = $em->getRepository(CarModelPetrolYear::class)
            ->findBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolYears)
        {
            foreach($modelPetrolYears as $modelPetrolYear)
            {
                $em->remove($modelPetrolYear);
            }
        }

        $modelPetrolSaleRegions = $em->getRepository(CarModelPetrolSaleRegion::class)
            ->findBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolSaleRegions)
        {
            foreach($modelPetrolSaleRegions as $modelPetrolSaleRegion)
            {
                $em->remove($modelPetrolSaleRegion);
            }
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        $carModelPetrolHandler = self::getContainer()->get(CarModelPetrolHandler::class);
        $carModelPetrolUid = new CarModelPetrolUid(CarModelPetrolUid::TEST);

        $carModelPetrolDTO = new CarModelPetrolDTO();
        $carModelPetrolDTO->setId($carModelPetrolUid);
        $carModelPetrolDTO->setModel(new CarModelUid(CarModelUid::TEST));
        $carModelPetrolDTO->setGeneration(new CarModelGenerationUid(CarModelGenerationUid::TEST));

        $carModelPetrolNameDTO = $carModelPetrolDTO->getName();
        $carModelPetrolNameDTO->setValue(new CarModelPetrolNameField(CarModelPetrolNameField::TEST));

        $carModelPetrolHPDTO = $carModelPetrolDTO->getHp();
        $carModelPetrolHPDTO->setValue(new CarModelPetrolHPField(CarModelPetrolHPField::TEST));

        $carModelPetrolKWDTO = $carModelPetrolDTO->getKw();
        $carModelPetrolKWDTO->setValue(new CarModelPetrolKWField(CarModelPetrolKWField::TEST));

        $carModelPetrolPSDTO = $carModelPetrolDTO->getPs();
        $carModelPetrolPSDTO->setValue(new CarModelPetrolPSField(CarModelPetrolPSField::TEST));

        $carModelPetrolYearDTO = new CarModelPetrolYearDTO();
        $carModelPetrolYearDTO->setValue(new CarModelPetrolYearField(CarModelPetrolYearField::TEST));
        $carModelPetrolYearDTO->setModelPetrol($carModelPetrolUid);
        $carModelPetrolDTO->addYear($carModelPetrolYearDTO);

        $carModelPetrolSaleRegionDTO = new CarModelPetrolSaleRegionDTO();
        $carModelPetrolSaleRegionDTO->setValue(new CarModelPetrolSaleRegionField(CarModelPetrolSaleRegionField::TEST));
        $carModelPetrolSaleRegionDTO->setModelPetrol($carModelPetrolUid);
        $carModelPetrolDTO->addSaleRegion($carModelPetrolSaleRegionDTO);

        $carModelPetrol = $carModelPetrolHandler->handle($carModelPetrolDTO);

        self::assertInstanceOf(CarModelPetrol::class, $carModelPetrol);
    }
}