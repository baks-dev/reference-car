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

namespace BaksDev\Reference\Car\Messenger\WheelSize\Wheel;

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelClassCheckerDTO;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelClassCheckerService;
use DateInterval;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserWheelInvariableHandler
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $Logger,
        private DeduplicatorInterface $Deduplicator,
        private CarModelWheelClassCheckerService $carModelWheelClassCheckerService,
    ) {}

    public function __invoke(ParserWheelMessage $message): void
    {
        /**
         * Разбивка значения шин на части
         *
         * Примеры:
         * 1) 255/45ZR21 106W
         * 2) OE 225/55ZR19 103Y
         * 3) OE 102V
         * 4) OE 103Y 108Y
         * 5) OE 265/35ZR21 101Y 305/30ZR21 104Y
         */
        $tire = $message->getTire();

        if(false === $message->isForced())
        {
            /** Делаем проверку дедупликатором */
            $Deduplicator = $this->Deduplicator
                ->namespace('reference-car')
                ->expiresAfter(DateInterval::createFromDateString('1 day'))
                ->deduplication([$tire, $message->getClassName(), md5(self::class)]);

            if($Deduplicator->isExecuted())
            {
                return;
            }
        }

        $tireParts = explode(' ', $tire);
        $result = [];
        $result['tireSize'] = isset($tireParts[1]) ? $tireParts[1] : null;


        /**
         * Делим на части размеры шины
         *
         * Пример:
         * 225/55ZR19
         */
        $sizePart = false;
        if($result['tireSize'] !== null)
        {
            $parts = array_filter(explode(' ', $result['tireSize']));
            $sizePart = current(array_filter($parts, fn($p) => str_contains($p, '/')));
        }


        if($sizePart !== false)
        {
            /**
             * Разбиваем параметры шин на переменные
             */
            $pattern = '/^(\d{3})\/(\d{2})([HVZ]?)([A-Za-z]?)(\d{2})$/';
            preg_match($pattern, $sizePart, $matches);

            if(true === empty($matches))
            {
                $this->Logger->critical(sprintf(
                    'reference-car: Ошибка разбиения параметров шины %s',
                    $sizePart
                ));

                return;
            }

            $wheelParam = [
                'width' => $matches[1],       // Ширина в мм
                'profile' => $matches[2], // Высота профиля
                'rim_diameter' => $matches[5],  // Диаметр диска в дюймах
            ];


            $CarModelWheelClassCheckerDTO = new CarModelWheelClassCheckerDTO(
                $message->getClassName(),
                $message->getTire(),
                $message->getBackspacing(),
                $message->getBar(),
                $wheelParam['rim_diameter'],
                $message->getOffsetRange(),
                $wheelParam['profile'],
                $message->getRim(),
                $message->getTireWeight(),
                $wheelParam['width'],
                $message->getModelPetrol()
            );


            // Проверяем есть ли класс шины. Если нет, то генерируем
            $this->carModelWheelClassCheckerService->checkCarModelWheel($CarModelWheelClassCheckerDTO);
        }

        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }
}
