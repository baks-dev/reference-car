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

namespace BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\Tests;

use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModels\Name\CarModelName as CarModelNameField;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Tests\CarBrandNewAdminUseCaseTest;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\CarModelDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\CarModelHandler;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-usecase')]
#[Group('reference-car-repository')]
#[Group('reference-car-controller')]
class CarModelNewAdminUseCaseTest extends KernelTestCase
{
    /**
     * Удаляем тестовые данные перед началом тестов
     */
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $model = $EntityManager
            ->getRepository(CarModel::class)
            ->findOneBy(['id' => CarModelUid::TEST]);

        if($model)
        {
            $EntityManager->remove($model);
        }

        $modelName = $EntityManager
            ->getRepository(CarModelName::class)
            ->findOneBy(['model' => CarModelUid::TEST]);

        if($modelName)
        {
            $EntityManager->remove($modelName);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }


    #[DependsOnClass(CarBrandNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        $CarModelHandler = self::getContainer()->get(CarModelHandler::class);

        $CarModelDTO = new CarModelDTO();
        $CarModelDTO
            ->setId(new CarModelUid(CarModelUid::TEST))
            ->setBrand(new CarBrandUid(CarBrandUid::TEST));

        $CarModelNameDTO = $CarModelDTO->getName();
        $CarModelNameDTO
            ->setValue(new CarModelNameField(CarModelNameField::TEST))
            ->setUrl(strtr(strtolower(CarModelNameField::TEST), ['(' => '', ')' => '', ' ' => '-', '/' => '-']));

        $CarModel = $CarModelHandler->handle($CarModelDTO);

        self::assertInstanceOf(CarModel::class, $CarModel);
    }
}