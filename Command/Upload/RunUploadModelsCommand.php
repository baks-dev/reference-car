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
use BaksDev\Reference\Car\Entity\CarModel\CarModel;
use BaksDev\Reference\Car\Repository\ExistCarModel\ExistCarModelInterface;
use BaksDev\Reference\Car\Type\CarModels\Models\CarModelsInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\CarModelDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModel\CarModelHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(name: 'baks:car:models-load', description: 'Загружает модели автомобилей из классов в базу данных')]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadModelsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarModelHandler $carModelHandler,
        private readonly ExistCarModelInterface $ExistCarModelRepository,
        #[AutowireIterator('baks.car.models')] private readonly iterable $carModels,
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
        $io->text("Загрузка моделей автомобилей");


        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;


        /** @var CarModelsInterface $carModel */
        foreach($this->carModels as $carModel)
        {
            /** Проверяем что модель не добавлена */
            $isExistCarModel = $this->ExistCarModelRepository->exist($carModel::getUid());

            if(true === $isExistCarModel)
            {
                continue;
            }


            $carModelDTO = new CarModelDTO();
            $carModelDTO->setId($carModel::getUid());
            $carModelDTO->setBrand($carModel->getBrandUid());

            $CarModelNameDTO = $carModelDTO->getName();
            $CarModelNameDTO
                ->setValue($carModel::getValue())
                ->setUrl(strtr(strtolower(
                    (string)$carModel::getValue()),
                    ['(' => '', ')' => '', ' ' => '-', '/' => '-']
                ));


            /**
             * Создаем новую модель
             */
            $carModel = $this->carModelHandler->handle($carModelDTO);


            /**
             * Выдаем сообщение в консоль об успехе загрузки бренда
             */
            if($carModel instanceof CarModel)
            {
                $count++;
                $io->text("Добавлена модель: {$carModel->getName()}");
            }
        }

        $io->text("Загружено моделей: ".$count);
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}