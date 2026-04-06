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
use BaksDev\Reference\Car\Entity\CarModelGeneration\CarModelGeneration;
use BaksDev\Reference\Car\Repository\ExistCarModelGeneration\ExistCarModelGenerationInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\CarModelGenerationsInterface;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\ModelGenerations\CarModelGenerationsNameInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelGeneration\CarModelGenerationHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

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
        #[AutowireIterator('baks.car.generations.name')] private readonly iterable $carModelGenerationsName
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

        $carModelGenerationsName = iterator_to_array($this->carModelGenerationsName);

        /** @var CarModelGenerationsInterface $carModelGeneration */
        foreach($this->carModelGenerations as $carModelGeneration)
        {
            /** Проверяем что модель не добавлена */
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
            $carModelGenerationDTO->setModel($carModelGeneration->getModelUid());

            /** @var CarModelGenerationsNameInterface $carModelGenerationsName */

            foreach($carModelGenerationsName as $carModelGenerationName)
            {
                if(true === $carModelGenerationName::equals($carModelGeneration::getUid()))
                {
                    $carModelGenerationNameDTO = $carModelGenerationDTO->getName();
                    $carModelGenerationNameDTO->setValue($carModelGenerationName::getValue());
                }
            }

            /**
             * Создаем новое поколение
             */
            $carModelGeneration = $this->carModelGenerationHandler->handle($carModelGenerationDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки модели
             */
            if($carModelGeneration instanceof CarModelGeneration)
            {
                $count++;
                $io->text("Добавлена модель: {$carModelGeneration->getName()}");
            }
        }

        $io->text("Загружено model generation: {$count}");
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}