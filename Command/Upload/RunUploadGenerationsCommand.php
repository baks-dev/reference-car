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
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Repository\ExistCarModelGeneration\ExistCarModelGenerationInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\CarModelGenerationsInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\Image\CarModelGenerationImageDTO;
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

#[AsCommand(
    name: 'baks:car:model-generations-load',
    description: 'Загружает бренды автомобилей из классов в базу данных',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadGenerationsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarModelGenerationHandler $carModelGenerationHandler,
        private readonly ExistCarModelGenerationInterface $ExistCarModelGenerationRepository,
        #[AutowireIterator('baks.car.generations')] private readonly iterable $carModelGenerations,
    )
    {
        parent::__construct();
    }


    /** Чем выше число - тем первым в итерации будет значение */
    public static function priority(): int
    {
        return 100;
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Загрузка поколений автомобилей');


        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;


        /** @var CarModelGenerationsInterface $carModelGeneration */
        foreach($this->carModelGenerations as $carModelGeneration)
        {
            /** Проверяем, что поколение не добавлено */
            $isExistCarModelGeneration = $this->ExistCarModelGenerationRepository->exist($carModelGeneration::getUid());

            if(true === $isExistCarModelGeneration)
            {
                continue;
            }


            /**
             * Создаем DTO для поколения вместе с названием поколения
             */
            $carModelGenerationDTO = new CarModelGenerationDTO();
            $carModelGenerationDTO->setId($carModelGeneration::getUid());
            $carModelGenerationDTO->setModel($carModelGeneration::getModelUid());

            $carModelGenerationNameDTO = $carModelGenerationDTO->getName();
            $carModelGenerationNameDTO
                ->setValue($carModelGeneration::getValue())
                ->setUrl(strtr(
                    strtolower((string) $carModelGeneration::getValue()),
                    ['(' => '', ')' => '', ' ' => '-', '/' => '-'],
                ));


            /**
             * Сохраняем данные об изображении поколения
             */

            /** Директория, в которой будет производиться поиск изображения по идентификтору поколения */
            $imagePath = implode(DIRECTORY_SEPARATOR, [
                rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
                'Resources',
                'upload',
                'car_model_generation_image',
                (string) $carModelGeneration::getUid(),
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
                        $CarModelGenerationImageDTO = new CarModelGenerationImageDTO()
                            ->setName((string) $carModelGeneration::getUid())
                            ->setExt($info->getExtension())
                            ->setSize($info->getSize());

                        $carModelGenerationDTO->setImage($CarModelGenerationImageDTO);

                        break;
                    }
                }
            }

            /**
             * Создаем новое поколение
             */
            $carModelGeneration = $this->carModelGenerationHandler->handle($carModelGenerationDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки поколения
             */
            if($carModelGeneration instanceof CarModelGeneration)
            {
                $count++;
                $io->text("Добавлено поколение: {$carModelGeneration->getName()}");
            }
        }


        $io->text("Загружено model generation: ".$count);
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}