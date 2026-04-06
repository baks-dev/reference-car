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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Tests;

use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName as CarBrandNameField;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandHandler;
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
class CarBrandNewAdminUseCaseTest extends KernelTestCase
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
        $brand = $em->getRepository(CarBrand::class)
            ->findOneBy(['id' => CarBrandUid::TEST]);

        if($brand)
        {
            $em->remove($brand);
        }

        $brandName = $em->getRepository(CarBrandName::class)
            ->findOneBy(['brand' => CarBrandUid::TEST]);

        if($brandName)
        {
            $em->remove($brandName);
        }

        $em->flush();
        $em->clear();
    }

    public function testUseCase(): void
    {

        $CarBrandHandler = self::getContainer()->get(CarBrandHandler::class);

        $carBrandDTO = new CarBrandDTO();
        $carBrandDTO->setId(new CarBrandUid(CarBrandUid::TEST));

        $CarBrandNameDTO = $carBrandDTO->getName();
        $CarBrandNameDTO->setValue(new CarBrandNameField(CarBrandNameField::TEST));

        $carBrand = $CarBrandHandler->handle($carBrandDTO);

        self::assertInstanceOf(CarBrand::class, $carBrand);
    }
}