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
use BaksDev\Reference\Car\Entity\CarModelYear\CarModelYear;
use BaksDev\Reference\Car\Repository\ExistCarModelYear\ExistCarModelYearInterface;
use BaksDev\Reference\Car\Type\CarModels\Year\Models\CarModelsYearInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelYear\CarModelYearDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelYear\CarModelYearHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'baks:car:model-years-load',
    description: 'Загружает бренды автомобилей из классов в базу данных',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadModelYearsCommand extends Command implements ProjectUpgradeInterface
{

    public function __construct(
        private readonly CarModelYearHandler $carModelYearHandler,
        private readonly ExistCarModelYearInterface $ExistCarModelYearRepository,

        #[AutowireIterator('baks.car.models.year')] private readonly iterable $carModelsYear
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
        echo 'Загрузка model year автомобилей'.PHP_EOL;

        /**
         * Счетчик загруженных элементов для вывода статистики
         */
        $count = 0;


        /** @var CarModelsYearInterface $carModelYear */
        foreach($this->carModelsYear as $carModelYear)
        {
            /** Проверяем что модель не добавлена */
            $isExistCarModelYear = $this->ExistCarModelYearRepository->exist($carModelYear::getUid());

            if(true === $isExistCarModelYear)
            {
                continue;
            }

            /**
             * Создаем DTO для model year вместе с названием model year
             */
            $carModelYearDTO = new CarModelYearDTO();
            $carModelYearDTO->setModel($carModelYear::getUid());
            $carModelYearDTO->setValue($carModelYear::getValue());

            /**
             * Создаем новую model year
             */
            $carModelYear = $this->carModelYearHandler->handle($carModelYearDTO);

            /**
             * Выдаем сообщение в консоль об успехе загрузки model year
             */
            if($carModelYear instanceof CarModelYear)
            {
                $count++;
                echo "Добавлен model year: {$carModelYear->getValue()}".PHP_EOL;
            }
        }

        echo "Загружено ModelYear: {$count}".PHP_EOL;
        echo "Загрузка завершена".PHP_EOL;
        return Command::SUCCESS;
    }
}