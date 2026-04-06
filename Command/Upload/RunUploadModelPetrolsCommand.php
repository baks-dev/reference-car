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
use BaksDev\Reference\Car\Entity\CarModelPetrol\CarModelPetrol;
use BaksDev\Reference\Car\Repository\ExistCarModelPetrol\ExistCarModelPetrolInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\HP\ModelPetrols\CarModelPetrolPowerHPInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\CarModelPetrolInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\KW\ModelPetrols\CarModelPetrolPowerKWInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Name\ModelPetrols\CarModelPetrolNameInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\PS\ModelPetrols\CarModelPetrolPowerPSInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\SaleRegion\ModelPetrols\CarModelPetrolSaleRegionInterface;
use BaksDev\Reference\Car\Type\CarModelPetrols\Year\ModelPetrols\CarModelPetrolYearInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolHandler;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolSaleRegion\CarModelPetrolSaleRegionDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolYear\CarModelPetrolYearDTO;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'baks:car:model-petrols-load',
    description: 'Загружает бренды автомобилей из классов в базу данных',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadModelPetrolsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarModelPetrolHandler $carModelPetrolHandler,
        private readonly ExistCarModelPetrolInterface $ExistCarModelPetrolRepository,

        #[AutowireIterator('baks.car.model.petrols')] private readonly iterable $carPetrols,
        #[AutowireIterator('baks.car.model.petrols.name')] private readonly iterable $carModelPetrolsNames,
        #[AutowireIterator('baks.car.model.petrols.year')] private readonly iterable $carModelPetrolsYears,
        #[AutowireIterator('baks.car.model.petrols.sale.region')] private readonly iterable $carModelPetrolsSaleRegions,
        #[AutowireIterator('baks.car.model.petrols.power.hp')] private readonly iterable $carModelPetrolsHPs,
        #[AutowireIterator('baks.car.model.petrols.power.kw')] private readonly iterable $carModelPetrolsKWs,
        #[AutowireIterator('baks.car.model.petrols.power.ps')] private readonly iterable $carModelPetrolsPSs,
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
        $io->text("Загрузка model petrol автомобилей");

        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;

        $carModelPetrolsNames = iterator_to_array($this->carModelPetrolsNames);
        $carModelPetrolsYears = iterator_to_array($this->carModelPetrolsYears);
        $carModelPetrolsSaleRegions = iterator_to_array($this->carModelPetrolsSaleRegions);
        $carModelPetrolsHPs = iterator_to_array($this->carModelPetrolsHPs);
        $carModelPetrolsKWs = iterator_to_array($this->carModelPetrolsKWs);
        $carModelPetrolsPSs = iterator_to_array($this->carModelPetrolsPSs);

        /** @var CarModelPetrolInterface $carPetrol */
        foreach($this->carPetrols as $carPetrol)
        {
            /** Проверяем что модель не добавлена */
            $isExistCarModelPetrol = $this->ExistCarModelPetrolRepository->exist($carPetrol::getUid());

            if(true === $isExistCarModelPetrol)
            {
                continue;
            }

            /**
             * Создаем DTO для model petrol вместе с названием model petrol
             */
            $carModelPetrolDTO = new CarModelPetrolDTO();
            $carModelPetrolDTO->setId($carPetrol::getUid());
            $carModelPetrolDTO->setModel($carPetrol->getModelUid());
            $carModelPetrolDTO->setGeneration($carPetrol::getModelGenerationUid());

            /** @var CarModelPetrolNameInterface $carModelPetrolName */
            foreach($carModelPetrolsNames as $carModelPetrolName)
            {
                if(true === $carModelPetrolName::equals($carPetrol::getUid()))
                {
                    $carModelPetrolNameDTO = $carModelPetrolDTO->getName();
                    $carModelPetrolNameDTO->setValue($carModelPetrolName::getValue());
                }
            }

            /** @var CarModelPetrolPowerHPInterface $carModelPetrolHP */
            foreach($carModelPetrolsHPs as $carModelPetrolHP)
            {
                if(true === $carModelPetrolHP::equals($carPetrol::getUid()))
                {
                    $carModelPetrolHPDTO = $carModelPetrolDTO->getHp();
                    //                    echo get_class($carModelPetrolHP);
                    $carModelPetrolHPDTO->setValue($carModelPetrolHP::getValue());
                }
            }

            /** @var CarModelPetrolPowerKWInterface $carModelPetrolKW */
            foreach($carModelPetrolsKWs as $carModelPetrolKW)
            {
                if(true === $carModelPetrolKW::equals($carPetrol::getUid()))
                {
                    $carModelPetrolKWDTO = $carModelPetrolDTO->getKw();
                    $carModelPetrolKWDTO->setValue($carModelPetrolKW::getValue());
                }
            }

            /** @var CarModelPetrolPowerPSInterface $carModelPetrolPS */
            foreach($carModelPetrolsPSs as $carModelPetrolPS)
            {
                if(true === $carModelPetrolPS::equals($carPetrol::getUid()))
                {
                    $carModelPetrolPSDTO = $carModelPetrolDTO->getPs();
                    $carModelPetrolPSDTO->setValue($carModelPetrolPS::getValue());
                }
            }

            $carModelPetrolDTO->getYear()->clear();

            /** @var CarModelPetrolYearInterface $carModelPetrolYear */
            foreach($carModelPetrolsYears as $carModelPetrolYear)
            {
                if(true === $carModelPetrolYear::equals($carPetrol::getUid()))
                {
                    $carModelPetrolYearDTO = new CarModelPetrolYearDTO();
                    $carModelPetrolYearDTO->setValue($carModelPetrolYear::getValue());
                    $carModelPetrolYearDTO->setModelPetrol($carPetrol::getUid());
                    $carModelPetrolDTO->addYear($carModelPetrolYearDTO);
                }
            }

            $carModelPetrolDTO->getSaleRegion()->clear();

            /** @var CarModelPetrolSaleRegionInterface $carModelPetrolSaleRegion */
            foreach($carModelPetrolsSaleRegions as $carModelPetrolSaleRegion)
            {
                if(true === $carModelPetrolSaleRegion::equals($carPetrol::getUid()))
                {
                    $carModelPetrolSaleRegionDTO = new CarModelPetrolSaleRegionDTO();
                    $carModelPetrolSaleRegionDTO->setValue($carModelPetrolSaleRegion::getValue());
                    $carModelPetrolSaleRegionDTO->setModelPetrol($carPetrol::getUid());
                    $carModelPetrolDTO->addSaleRegion($carModelPetrolSaleRegionDTO);
                }
            }

            /**
             * Создаем новую model petrol
             */
            $carModelPetrol = $this->carModelPetrolHandler->handle($carModelPetrolDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки бренда
             */
            if($carModelPetrol instanceof CarModelPetrol)
            {
                $count++;
                $io->text("Добавлена model petrol: {$carModelPetrol->getName()}");
            }
        }

        $io->text("Загружено ModelPetrol: {$count}");
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}