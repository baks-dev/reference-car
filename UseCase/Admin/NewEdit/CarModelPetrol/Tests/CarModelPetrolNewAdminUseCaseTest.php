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
use BaksDev\Reference\Car\Entity\CarModelPetrol\Year\CarModelPetrolYear;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\CarModelPetrolHP as CarModelPetrolHPField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\CarModelPetrolUid;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\CarModelPetrolKW as CarModelPetrolKWField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\CarModelPetrolName as CarModelPetrolNameField;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\CarModelPetrolPS as CarModelPetrolPSField;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\CarModelPetrolYear as CarModelPetrolYearField;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\Tests\CarModelGenerationNewAdminUseCaseTest;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-repository')]
#[Group('reference-car-usecase')]
final class CarModelPetrolNewAdminUseCaseTest extends KernelTestCase
{
    /**
     * Удаляем тестовые данные перед началом тестов
     */
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $carModelPetrol = $EntityManager
            ->getRepository(CarModelPetrol::class)
            ->findOneBy(['id' => CarModelPetrolUid::TEST]);

        if($carModelPetrol)
        {
            $EntityManager->remove($carModelPetrol);
        }

        $modelPetrolName = $EntityManager
            ->getRepository(CarModelPetrolName::class)
            ->findOneBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolName)
        {
            $EntityManager->remove($modelPetrolName);
        }

        $modelPetrolHP = $EntityManager
            ->getRepository(CarModelPetrolHP::class)
            ->findOneBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolHP)
        {
            $EntityManager->remove($modelPetrolHP);
        }

        $modelPetrolKW = $EntityManager
            ->getRepository(CarModelPetrolKW::class)
            ->findOneBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolKW)
        {
            $EntityManager->remove($modelPetrolKW);
        }

        $modelPetrolPS = $EntityManager
            ->getRepository(CarModelPetrolPS::class)
            ->findOneBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolPS)
        {
            $EntityManager->remove($modelPetrolPS);
        }

        $modelPetrolYears = $EntityManager
            ->getRepository(CarModelPetrolYear::class)
            ->findBy(['petrol' => CarModelPetrolUid::TEST]);

        if($modelPetrolYears)
        {
            foreach($modelPetrolYears as $modelPetrolYear)
            {
                $EntityManager->remove($modelPetrolYear);
            }
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }


    #[DependsOnClass(CarModelGenerationNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        $carModelPetrolHandler = self::getContainer()->get(CarModelPetrolHandler::class);

        $carModelPetrolDTO = new CarModelPetrolDTO();
        $carModelPetrolDTO->setGeneration(new CarModelGenerationUid());

        $carModelPetrolNameDTO = $carModelPetrolDTO->getName();
        $carModelPetrolNameDTO
            ->setValue(new CarModelPetrolNameField(CarModelPetrolNameField::TEST))
            ->setUrl(strtr(
                strtolower(CarModelPetrolNameField::TEST),
                ['(' => '', ')' => '', ' ' => '-', '/' => '-']
            ));

        $carModelPetrolHPDTO = $carModelPetrolDTO->getHp();
        $carModelPetrolHPDTO->setValue(new CarModelPetrolHPField(CarModelPetrolHPField::TEST));

        $carModelPetrolKWDTO = $carModelPetrolDTO->getKw();
        $carModelPetrolKWDTO->setValue(new CarModelPetrolKWField(CarModelPetrolKWField::TEST));

        $carModelPetrolPSDTO = $carModelPetrolDTO->getPs();
        $carModelPetrolPSDTO->setValue(new CarModelPetrolPSField(CarModelPetrolPSField::TEST));

        $carModelPetrolYearDTO = $carModelPetrolDTO->getYear();
        $carModelPetrolYearDTO->setValue(new CarModelPetrolYearField(CarModelPetrolYearField::TEST));

        $carModelPetrol = $carModelPetrolHandler->handle($carModelPetrolDTO);

        self::assertInstanceOf(CarModelPetrol::class, $carModelPetrol);
    }
}