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
use BaksDev\Reference\Car\Type\CarModelWheels\Backspacing\ModelWheels\CarModelWheelsBackspacingInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Bar\ModelWheels\CarModelWheelsBarInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\ModelWheels\CarModelWheelsDiameterInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\CarModelWheelsInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\OffsetRange\ModelWheels\CarModelWheelsOffsetRangeInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Profile\ModelWheels\CarModelWheelsProfileInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Rim\ModelWheels\CarModelWheelsRimInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\TireWeight\ModelWheels\CarModelWheelsTireWeightInterface;
use BaksDev\Reference\Car\Type\CarModelWheels\Width\ModelWheels\CarModelWheelsWidthInterface;
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
        #[AutowireIterator('baks.car.model.wheels.diameter')] private readonly iterable $carModelWheelDiameters,
        #[AutowireIterator('baks.car.model.wheels.bar')] private readonly iterable $carModelWheelBars,
        #[AutowireIterator('baks.car.model.wheels.backspacing')] private readonly iterable $carModelWheelBackspacings,
        #[AutowireIterator('baks.car.model.wheels.offset.range')] private readonly iterable $carModelWheelOffsetRanges,
        #[AutowireIterator('baks.car.model.wheels.profile')] private readonly iterable $carModelWheelProfiles,
        #[AutowireIterator('baks.car.model.wheels.rim')] private readonly iterable $carModelWheelRims,
        #[AutowireIterator('baks.car.model.wheels.tire.weight')] private readonly iterable $carModelWheelTireWeights,
        #[AutowireIterator('baks.car.model.wheels.width')] private readonly iterable $carModelWheelWidths,

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

        $carModelWheelDiameters = iterator_to_array($this->carModelWheelDiameters);
        $carModelWheelBars = iterator_to_array($this->carModelWheelBars);
        $carModelWheelBackspacings = iterator_to_array($this->carModelWheelBackspacings);
        $carModelWheelOffsetRanges = iterator_to_array($this->carModelWheelOffsetRanges);
        $carModelWheelProfiles = iterator_to_array($this->carModelWheelProfiles);
        $carModelWheelRims = iterator_to_array($this->carModelWheelRims);
        $carModelWheelTireWeights = iterator_to_array($this->carModelWheelTireWeights);
        $carModelWheelWidths = iterator_to_array($this->carModelWheelWidths);

        /** @var CarModelWheelsInterface $carModelWheel */
        foreach($this->carModelWheels as $carModelWheel)
        {
            /** Проверяем что колесо не добавлено */
            $isExistCarModelWheel = $this->ExistCarModelWheelRepository->exist($carModelWheel::getUid());

            /**
             * Если модель найдена, то пропускаем его загрузку
             */
            if(true === $isExistCarModelWheel)
            {
                continue;
            }

            /**
             * Создаем DTO для бренда вместе с названием бренда
             */
            $carModelWheelDTO = new CarModelWheelDTO();
            $carModelWheelDTO->setId($carModelWheel::getUid());
            $carModelWheelDTO->setModelPetrol($carModelWheel->getModelPetrolUid());

            /** @var CarModelWheelsDiameterInterface $carModelWheelDiameter */
            foreach($carModelWheelDiameters as $carModelWheelDiameter)
            {
                if(true === $carModelWheelDiameter::equals($carModelWheel::getUid()))
                {
                    $carModelDiameterDTO = $carModelWheelDTO->getDiameter();
                    $carModelDiameterDTO->setValue($carModelWheelDiameter::getValue());
                }
            }

            /** @var CarModelWheelsBarInterface $carModelWheelBar */
            foreach($carModelWheelBars as $carModelWheelBar)
            {
                if(true === $carModelWheelBar::equals($carModelWheel::getUid()))
                {
                    $carModelBarDTO = $carModelWheelDTO->getBar();
                    $carModelBarDTO->setValue($carModelWheelBar::getValue());
                }
            }

            /** @var CarModelWheelsBackspacingInterface $carModelWheelBackspacing */
            foreach($carModelWheelBackspacings as $carModelWheelBackspacing)
            {
                if(true === $carModelWheelBackspacing::equals($carModelWheel::getUid()))
                {
                    $carModelBackspacingDTO = $carModelWheelDTO->getBackspacing();
                    $carModelBackspacingDTO->setValue($carModelWheelBackspacing::getValue());
                }
            }

            /** @var CarModelWheelsOffsetRangeInterface $carModelWheelOffsetRange */
            foreach($carModelWheelOffsetRanges as $carModelWheelOffsetRange)
            {
                if(true === $carModelWheelOffsetRange::equals($carModelWheel::getUid()))
                {
                    $carModelOffsetRangeDTO = $carModelWheelDTO->getOffsetRange();
                    $carModelOffsetRangeDTO->setValue($carModelWheelOffsetRange::getValue());
                }
            }

            /** @var CarModelWheelsProfileInterface $carModelWheelProfile */
            foreach($carModelWheelProfiles as $carModelWheelProfile)
            {
                if(true === $carModelWheelProfile::equals($carModelWheel::getUid()))
                {
                    $carModelProfileDTO = $carModelWheelDTO->getProfile();
                    $carModelProfileDTO->setValue($carModelWheelProfile::getValue());
                }
            }

            /** @var CarModelWheelsRimInterface $carModelWheelRim */
            foreach($carModelWheelRims as $carModelWheelRim)
            {
                if(true === $carModelWheelRim::equals($carModelWheel::getUid()))
                {
                    $carModelRimDTO = $carModelWheelDTO->getRim();
                    $carModelRimDTO->setValue($carModelWheelRim::getValue());
                }
            }

            /** @var CarModelWheelsTireWeightInterface $carModelWheelTireWeight */
            foreach($carModelWheelTireWeights as $carModelWheelTireWeight)
            {
                if(true === $carModelWheelTireWeight::equals($carModelWheel::getUid()))
                {
                    $carModelTireWeightDTO = $carModelWheelDTO->getTireWeight();
                    $carModelTireWeightDTO->setValue($carModelWheelTireWeight::getValue());
                }
            }

            /** @var CarModelWheelsWidthInterface $carModelWheelWidth */
            foreach($carModelWheelWidths as $carModelWheelWidth)
            {
                if(true === $carModelWheelWidth::equals($carModelWheel::getUid()))
                {
                    $carModelWidthDTO = $carModelWheelDTO->getWidth();
                    $carModelWidthDTO->setValue($carModelWheelWidth::getValue());
                }
            }

            /**
             * Создаем новое колесо
             */
            $carModelWheel = $this->carModelWheelHandler->handle($carModelWheelDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки модели
             */
            if($carModelWheel instanceof CarModelWheel)
            {
                $count++;
                $io->text("Добавлено колесо: {$carModelWheel->getDiameter()}");
            }
        }

        $io->text("Загружено моделей: {$count}");
        $io->text("Загрузка завершена");
        echo "Загружено моделей: {$count}".PHP_EOL;
        echo "Загрузка завершена".PHP_EOL;
        return Command::SUCCESS;
    }
}