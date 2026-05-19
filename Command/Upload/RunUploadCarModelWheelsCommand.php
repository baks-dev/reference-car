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
use BaksDev\Reference\Car\Entity\CarModelWheel\CarModelWheel;
use BaksDev\Reference\Car\Repository\ExistCarModelWheel\ExistCarModelWheelInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\ModelWheels\CarModelWheelsInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelWheel\CarModelWheelDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelWheel\CarModelWheelHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'baks:car:model-wheels-load',
    description: 'Загружает колеса автомобилей из классов в базу данных',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadCarModelWheelsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarModelWheelHandler $carModelWheelHandler,
        private readonly ExistCarModelWheelInterface $ExistCarModelWheelRepository,
        #[AutowireIterator('baks.car.model.wheels')] private readonly iterable $carModelWheels,
    )
    {
        parent::__construct();
    }

    /** Чем выше число - тем первым в итерации будет значение */
    public static function priority(): int
    {
        return 100;
    }

    protected
    function execute(
        InputInterface $input,
        OutputInterface $output
    ): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Загрузка шин автомобилей');


        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;


        /** @var CarModelWheelsInterface $carModelWheel */
        foreach($this->carModelWheels as $carModelWheel)
        {
            /** Проверяем что колесо не добавлено */
            $isExistCarModelWheel = $this->ExistCarModelWheelRepository->exist($carModelWheel::getUid());


            /**
             * Если шина найдена, то пропускаем его загрузку
             */
            if(true === $isExistCarModelWheel)
            {
                continue;
            }


            /**
             * Создаем DTO для шины
             */
            $carModelWheelDTO = new CarModelWheelDTO();

            $carModelWheelDTO->setId($carModelWheel::getUid());

            $carModelWheelDTO->setPetrol($carModelWheel::getModelPetrolUid());

            $carModelWheelBackspacingDTO = $carModelWheelDTO->getBackspacing();
            $carModelWheelBackspacingDTO->setValue($carModelWheel::getBackspacingValue());

            $carModelWheelBarDTO = $carModelWheelDTO->getBar();
            $carModelWheelBarDTO->setValue($carModelWheel::getBarValue());

            $carModelWheelDiameterDTO = $carModelWheelDTO->getDiameter();
            $carModelWheelDiameterDTO->setValue($carModelWheel::getDiameterValue());

            $carModelWheelOffsetRangeDTO = $carModelWheelDTO->getOffsetRange();
            $carModelWheelOffsetRangeDTO->setValue($carModelWheel::getOffsetRangeValue());

            $carModelWheelProfileDTO = $carModelWheelDTO->getProfile();
            $carModelWheelProfileDTO->setValue($carModelWheel::getProfileValue());

            $carModelWheelRimDTO = $carModelWheelDTO->getRim();
            $carModelWheelRimDTO->setValue($carModelWheel::getRimValue());

            $carModelWheelTireWeightDTO = $carModelWheelDTO->getTireWeight();
            $carModelWheelTireWeightDTO->setValue($carModelWheel::getTireWeightValue());

            $carModelWheelWidthDTO = $carModelWheelDTO->getWidth();
            $carModelWheelWidthDTO->setValue($carModelWheel::getWidthValue());


            /**
             * Создаем новое колесо
             */
            $carModelWheel = $this->carModelWheelHandler->handle($carModelWheelDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки шины
             */
            if($carModelWheel instanceof CarModelWheel)
            {
                $count++;
                $io->text("Добавлено колесо: {$carModelWheel->getDiameter()}");
            }
        }

        $io->text("Загружено шин: ".$count);
        $io->text("Загрузка завершена");
        echo "Загружено шин: ".$count.PHP_EOL;
        echo "Загрузка завершена".PHP_EOL;
        return Command::SUCCESS;
    }
}