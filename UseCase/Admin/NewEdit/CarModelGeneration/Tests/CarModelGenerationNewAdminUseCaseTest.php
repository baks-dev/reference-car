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

use BaksDev\Core\BaksDevCoreBundle;
use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Image\CarModelGenerationImage;
use BaksDev\Reference\Car\Entity\CarModelGeneration\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName as CarModelGenerationNameField;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\Tests\CarModelNewAdminUseCaseTest;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\Image\CarModelGenerationImageDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DependsOnClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-repository')]
#[Group('reference-car-usecase')]
class CarModelGenerationNewAdminUseCaseTest extends KernelTestCase
{
    /**
     * Удаляем тестовые данные перед началом тестов
     */
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $model = $EntityManager
            ->getRepository(CarModelGeneration::class)
            ->findOneBy(['id' => CarModelGenerationUid::TEST]);

        if($model)
        {
            $EntityManager->remove($model);
        }

        $modelName = $EntityManager
            ->getRepository(CarModelGenerationName::class)
            ->findOneBy(['generation' => CarModelGenerationUid::TEST]);

        if($modelName)
        {
            $EntityManager->remove($modelName);
        }

        $generationImage = $EntityManager
            ->getRepository(CarModelGenerationImage::class)
            ->findOneBy(['generation' => CarModelGenerationUid::TEST]);

        if($generationImage)
        {
            $EntityManager->remove($generationImage);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }


    #[DependsOnClass(CarModelNewAdminUseCaseTest::class)]
    public function testUseCase(): void
    {
        $CarModelGenerationHandler = self::getContainer()->get(CarModelGenerationHandler::class);

        $CarModelGenerationDTO = new CarModelGenerationDTO();
        $CarModelGenerationDTO->setModel(new CarModelUid());

        $CarModelGenerationNameDTO = $CarModelGenerationDTO->getName();
        $CarModelGenerationNameDTO
            ->setValue(new CarModelGenerationNameField(CarModelGenerationNameField::TEST))
            ->setUrl(strtr(
                strtolower(CarModelGenerationNameField::TEST),
                ['(' => '', ')' => '', ' ' => '-', '/' => '-']
            ));


        /**
         * Изображние поколения
         */

        $containerBag = self::getContainer()->get(ContainerBagInterface::class);
        $Filesystem = self::getContainer()->get(Filesystem::class);


        /** Создаем путь к тестовой директории */
        $testUploadDir = implode(
            DIRECTORY_SEPARATOR,
            [$containerBag->get('kernel.project_dir'), 'public', 'upload', 'tests']
        );


        $Filesystem->copy(
            BaksDevCoreBundle::PATH.implode(
                DIRECTORY_SEPARATOR,
                ['Resources', 'assets', 'img', 'empty.webp']
            ),
            $testUploadDir.DIRECTORY_SEPARATOR.'photo.webp'
        );

        $filePhoto = new File($testUploadDir.DIRECTORY_SEPARATOR.'photo.webp', false);


        /** Тестируем добавление фото */
        $image = new CarModelGenerationImageDTO()
            ->setFile($filePhoto)
            ->setExt('webp')
            ->setName('test')
            ->setSize(1);

        $CarModelGenerationDTO->setImage($image);


        $CarModelGeneration = $CarModelGenerationHandler->handle($CarModelGenerationDTO);

        self::assertInstanceOf(CarModelGeneration::class, $CarModelGeneration);
    }
}