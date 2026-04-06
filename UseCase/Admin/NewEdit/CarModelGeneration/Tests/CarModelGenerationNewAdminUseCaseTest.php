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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\Tests;

use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName as CarModelGenerationNameField;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationHandler;
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
class CarModelGenerationNewAdminUseCaseTest extends KernelTestCase
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
        $model = $em->getRepository(CarModelGeneration::class)
            ->findOneBy(['id' => CarModelGenerationUid::TEST]);

        if($model)
        {
            $em->remove($model);
        }

        $modelName = $em->getRepository(CarModelGenerationName::class)
            ->findOneBy(['modelGeneration' => CarModelGenerationUid::TEST]);

        if($modelName)
        {
            $em->remove($modelName);
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {
        $carModelGenerationHandler = self::getContainer()->get(CarModelGenerationHandler::class);

        $carModelGenerationDTO = new CarModelGenerationDTO();
        $carModelGenerationDTO->setId(new CarModelGenerationUid(CarModelGenerationUid::TEST));
        $carModelGenerationDTO->setModel(new CarModelUid(CarModelUid::TEST));

        $carModelGenerationNameDTO = $carModelGenerationDTO->getName();
        $carModelGenerationNameDTO->setValue(new CarModelGenerationNameField(CarModelGenerationNameField::TEST));

        $carModelGeneration = $carModelGenerationHandler->handle($carModelGenerationDTO);

        self::assertInstanceOf(CarModelGeneration::class, $carModelGeneration);
    }
}