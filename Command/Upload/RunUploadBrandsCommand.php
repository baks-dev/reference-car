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

namespace BaksDev\Reference\Car\Command\Upload;

use BaksDev\Core\Command\Update\ProjectUpgradeInterface;
use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Entity\CarBrand\CarBrand;
use BaksDev\Reference\Car\Repository\ExistCarBrand\ExistCarBrandInterface;
use BaksDev\Reference\Car\Type\CarBrands\Brands\CarBrandsInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\CarBrandHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarBrand\Image\CarBrandImageDTO;
use DirectoryIterator;
use SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(name: 'baks:car:brands-load', description: 'Загружает бренды автомобилей из классов в базу данных')]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadBrandsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarBrandHandler $CarBrandHandler,
        private readonly ExistCarBrandInterface $ExistCarBrandRepository,
        #[AutowireIterator('baks.car.brands')] private readonly iterable $carBrands,
    )
    {
        parent::__construct();
    }


    /** Чем выше число - тем первее в итерации будет значение */
    public static function priority(): int
    {
        return 100;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text("Загрузка брендов автомобилей");


        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;


        /** @var CarBrandsInterface $carBrand */
        foreach($this->carBrands as $carBrand)
        {
            /** Проверяем что бренд не добавлен */
            $isExistCarBrand = $this->ExistCarBrandRepository->exist($carBrand::getUid());

            if(true === $isExistCarBrand)
            {
                continue;
            }


            $CarBrandDTO = new CarBrandDTO();

            $CarBrandDTO->setId($carBrand::getUid());

            $CarBrandNameDTO = $CarBrandDTO->getName();
            $CarBrandNameDTO
                ->setValue($carBrand::getValue())
                ->setUrl(strtr(
                    strtolower((string)$carBrand::getValue()),
                    ['(' => '', ')' => '', ' ' => '-', '/' => '-']
                ));


            /**
             * Сохраняем данные об изображении бренда
             */

            /** Директория, в которой будет производиться поиск изображения по идентификтору бренда */
            $imagePath = implode(DIRECTORY_SEPARATOR, [
                rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
                'Resources',
                'upload',
                'car_brand_image',
                (string)$carBrand::getUid()
            ]);

            $Filesystem = new Filesystem();

            if($Filesystem->exists($imagePath))
            {
                $directory = new DirectoryIterator($imagePath);


                /** @var SplFileInfo $info */
                foreach($directory as $info)
                {
                    if($info->isFile() === false)
                    {
                        continue;
                    }

                    if(false === in_array($info->getExtension(), ['png', 'gif', 'jpg', 'jpeg', 'webp']))
                    {
                        continue;
                    }

                    if(true === str_starts_with($info->getFilename(), 'image'))
                    {
                        $CarBrandImageDTO = new CarBrandImageDTO()
                            ->setName((string)$carBrand::getUid())
                            ->setExt($info->getExtension())
                            ->setSize($info->getSize());

                        $CarBrandDTO->setImage($CarBrandImageDTO);

                        break;
                    }
                }
            }


            /**
             * Создаем новый бренд
             */
            $carBrand = $this->CarBrandHandler->handle($CarBrandDTO);


            /**
             * Выдаем сообщение в консоль об успехе загрузки бренда
             */
            if($carBrand instanceof CarBrand)
            {
                $count++;
                $io->text("Добавлен бренд: ".$carBrand->getName());
            }
        }

        $io->text("Загружено элементов: ".$count);
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}
