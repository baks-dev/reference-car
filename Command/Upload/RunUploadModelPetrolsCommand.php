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
use BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols\CarModelPetrolInterface;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolDTO;
use BaksDev\Reference\Car\UseCase\Admin\NewEdit\CarModelPetrol\CarModelPetrolHandler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

#[AsCommand(
    name: 'baks:car:model-petrols-load',
    description: 'Загружает комплектации автомобилей из классов в базу данных',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunUploadModelPetrolsCommand extends Command implements ProjectUpgradeInterface
{
    public function __construct(
        private readonly CarModelPetrolHandler $carModelPetrolHandler,
        private readonly ExistCarModelPetrolInterface $ExistCarModelPetrolRepository,
        #[AutowireIterator('baks.car.model.petrols')] private readonly iterable $carPetrols,
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

            $carModelPetrolDTO->setGeneration($carPetrol::getModelGenerationUid());

            $carModelPetrolNameDTO = $carModelPetrolDTO->getName();
            $carModelPetrolNameDTO
                ->setValue($carPetrol::getNameValue())
                ->setUrl(strtr(strtolower(
                    (string)$carPetrol::getNameValue()),
                    ['(' => '', ')' => '', ' ' => '-', '/' => '-']
                ));

            $carModelPetrolHPDTO = $carModelPetrolDTO->getHp();
            $carModelPetrolHPDTO->setValue($carPetrol::getHpValue());

            $carModelPetrolKWDTO = $carModelPetrolDTO->getKw();
            $carModelPetrolKWDTO->setValue($carPetrol::getKWValue());

            $carModelPetrolPSDTO = $carModelPetrolDTO->getPs();
            $carModelPetrolPSDTO->setValue($carPetrol::getPSValue());

            $carModelPetrolYearDTO = $carModelPetrolDTO->getYear();
            $carModelPetrolYearDTO->setValue($carPetrol::getPetrolYearValue());


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


        $io->text("Загружено ModelPetrol: ".$count);
        $io->text("Загрузка завершена");
        return Command::SUCCESS;
    }
}