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

namespace BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\Tests;

use BaksDev\Core\Doctrine\DBALQueryBuilder;
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Entity\CarModel\Name\CarModelName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\CarModelDeleteDTO;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModel\CarModelDeleteHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\Tests\CarModelNewAdminUseCaseTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-usecase')]
final class CarModelDeleteHandlerTest extends KernelTestCase
{
    #[DependsOnClass(CarModelNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $CarModel = $EntityManager
            ->getRepository(CarModel::class)
            ->findOneBy(['id' => CarModelUid::TEST]);


        /** @see CarModelDeleteDTO */
        $CarModelDeleteDTO = new CarModelDeleteDTO();
        $CarModel->getDto($CarModelDeleteDTO);


        /** @var CarModelDeleteHandler $CarModelDeleteHandler */
        $CarModelDeleteHandler = self::getContainer()->get(CarModelDeleteHandler::class);

        $handle = $CarModelDeleteHandler->handle($CarModelDeleteDTO);


        self::assertTrue(($handle instanceof CarModel), $handle.': Ошибка ');
    }

    #[DependsOnClass(CarModelNewAdminUseCaseTest::class)]
    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal
            ->from(CarModel::class)
            ->where('id = :id')
            ->setParameter('id', CarModelUid::TEST, CarModelUid::TYPE);

        self::assertFalse($dbal->fetchExist());
    }

    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $main = $EntityManager
            ->getRepository(CarModel::class)
            ->findOneBy(['id' => CarModelUid::TEST]);

        if($main)
        {
            $EntityManager->remove($main);
        }

        $name = $EntityManager
            ->getRepository(CarModelName::class)
            ->findBy(['model' => CarModelUid::TEST]);

        foreach($name as $remove)
        {
            $EntityManager->remove($remove);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
}