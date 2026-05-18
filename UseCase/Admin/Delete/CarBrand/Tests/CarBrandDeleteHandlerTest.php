<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\UseCase\Admin\Delete\CarBrand\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImage;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarBrand\CarBrandDeleteDTO;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarBrand\CarBrandDeleteHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Tests\CarBrandNewAdminUseCaseTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-usecase')]
final class CarBrandDeleteHandlerTest extends KernelTestCase
{
    #[DependsOnClass(CarBrandNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $CarBrand = $EntityManager
            ->getRepository(CarBrand::class)
            ->findOneBy(['id' => CarBrandUid::TEST]);


        /** @see CarBrandDeleteDTO */
        $CarBrandDeleteDTO = new CarBrandDeleteDTO();
        $CarBrand->getDto($CarBrandDeleteDTO);


        /** @var CarBrandDeleteHandler $CarBrandDeleteHandler */
        $CarBrandDeleteHandler = self::getContainer()->get(CarBrandDeleteHandler::class);

        $handle = $CarBrandDeleteHandler->handle($CarBrandDeleteDTO);


        self::assertTrue(($handle instanceof CarBrand), $handle.': Ошибка ');
    }

    #[DependsOnClass(CarBrandNewAdminUseCaseTest::class)]
    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal
            ->from(CarBrand::class)
            ->where('id = :id')
            ->setParameter('id', CarBrandUid::TEST, CarBrandUid::TYPE);

        self::assertFalse($dbal->fetchExist());
    }

    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $main = $EntityManager
            ->getRepository(CarBrand::class)
            ->findOneBy(['id' => CarBrandUid::TEST]);

        if($main)
        {
            $EntityManager->remove($main);
        }

        $name = $EntityManager
            ->getRepository(CarBrandName::class)
            ->findBy(['brand' => CarBrandUid::TEST]);

        foreach($name as $remove)
        {
            $EntityManager->remove($remove);
        }

        $image = $EntityManager
            ->getRepository(CarBrandImage::class)
            ->findBy(['brand' => CarBrandUid::TEST]);

        foreach($image as $remove)
        {
            $EntityManager->remove($remove);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
}