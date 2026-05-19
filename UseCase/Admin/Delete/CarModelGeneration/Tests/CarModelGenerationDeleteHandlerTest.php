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
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModelGeneration\CarModelGenerationDeleteDTO;
use BaksDev\Reference\Car\UseCase\Admin\Delete\CarModelGeneration\CarModelGenerationDeleteHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\Tests\CarModelGenerationNewAdminUseCaseTest;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;


#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-usecase')]
final class CarModelGenerationDeleteHandlerTest extends KernelTestCase
{
    #[DependsOnClass(CarModelGenerationNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $CarModelGeneration = $EntityManager
            ->getRepository(CarModelGeneration::class)
            ->findOneBy(['id' => CarModelGenerationUid::TEST]);


        /** @see CarModelGenerationDeleteDTO */
        $CarModelGenerationDeleteDTO = new CarModelGenerationDeleteDTO();
        $CarModelGeneration->getDto($CarModelGenerationDeleteDTO);


        /** @var CarModelGenerationDeleteHandler $CarModelGenerationDeleteHandler */
        $CarModelGenerationDeleteHandler = self::getContainer()->get(CarModelGenerationDeleteHandler::class);

        $handle = $CarModelGenerationDeleteHandler->handle($CarModelGenerationDeleteDTO);


        self::assertTrue(($handle instanceof CarModelGeneration), $handle.': Ошибка ');
    }

    #[DependsOnClass(CarModelGenerationNewAdminUseCaseTest::class)]
    public function testComplete(): void
    {
        /** @var DBALQueryBuilder $dbal */
        $dbal = self::getContainer()->get(DBALQueryBuilder::class);

        $dbal->createQueryBuilder(self::class);

        $dbal
            ->from(CarModelGeneration::class)
            ->where('id = :id')
            ->setParameter('id', CarModelGenerationUid::TEST, CarModelGenerationUid::TYPE);

        self::assertFalse($dbal->fetchExist());
    }

    public static function tearDownAfterClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $main = $EntityManager
            ->getRepository(CarModelGeneration::class)
            ->findOneBy(['id' => CarModelGenerationUid::TEST]);

        if($main)
        {
            $EntityManager->remove($main);
        }

        $name = $EntityManager
            ->getRepository(CarModelGenerationName::class)
            ->findBy(['generation' => CarModelGenerationUid::TEST]);

        foreach($name as $remove)
        {
            $EntityManager->remove($remove);
        }

        $image = $EntityManager
            ->getRepository(CarModelGenerationImage::class)
            ->findBy(['generation' => CarModelGenerationUid::TEST]);

        foreach($image as $remove)
        {
            $EntityManager->remove($remove);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
}