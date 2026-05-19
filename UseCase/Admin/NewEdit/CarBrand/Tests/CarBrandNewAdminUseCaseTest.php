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

use BaksDev\Core\BaksDevCoreBundle;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Entity\CarBrand\Image\CarBrandImage;
use BaksDev\Reference\Car\Entity\CarBrand\Name\CarBrandName;
use BaksDev\Reference\Car\Type\CarBrands\Id\CarBrandUid;
use BaksDev\Reference\Car\Type\CarBrands\Name\CarBrandName as CarBrandNameField;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Image\CarBrandImageDTO;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

#[When(env: 'test')]
#[Group('reference-car')]
#[Group('reference-car-controller')]
#[Group('reference-car-repository')]
#[Group('reference-car-usecase')]
final class CarBrandNewAdminUseCaseTest extends KernelTestCase
{
    /**
     * Удаляем тестовые данные перед началом тестов
     */
    public static function setUpBeforeClass(): void
    {
        /** @var EntityManagerInterface $EntityManager */
        $EntityManager = self::getContainer()->get(EntityManagerInterface::class);

        $brand = $EntityManager
            ->getRepository(CarBrand::class)
            ->findOneBy(['id' => CarBrandUid::TEST]);

        if($brand)
        {
            $EntityManager->remove($brand);
        }

        $brandName = $EntityManager
            ->getRepository(CarBrandName::class)
            ->findOneBy(['brand' => CarBrandUid::TEST]);

        if($brandName)
        {
            $EntityManager->remove($brandName);
        }

        $brandImage = $EntityManager
            ->getRepository(CarBrandImage::class)
            ->findOneBy(['brand' => CarBrandUid::TEST]);

        if($brandImage)
        {
            $EntityManager->remove($brandImage);
        }

        $EntityManager->flush();
        $EntityManager->clear();
    }
    
    
    public function testUseCase(): void
    {
        $CarBrandHandler = self::getContainer()->get(CarBrandHandler::class);

        $CarBrandDTO = new CarBrandDTO();
        $CarBrandDTO->setId(new CarBrandUid());

        $CarBrandNameDTO = $CarBrandDTO->getName();
        $CarBrandNameDTO
            ->setValue(new CarBrandNameField(CarBrandNameField::TEST))
            ->setUrl(strtr(strtolower(CarBrandNameField::TEST), ['(' => '', ')' => '', ' ' => '-', '/' => '-']));


        /**
         * Изображние бренда
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
        $image = new CarBrandImageDTO()
            ->setFile($filePhoto)
            ->setExt('webp')
            ->setName('test')
            ->setSize(1);

        $CarBrandDTO->setImage($image);


        $carBrand = $CarBrandHandler->handle($CarBrandDTO);

        self::assertInstanceOf(CarBrand::class, $carBrand);
    }
}