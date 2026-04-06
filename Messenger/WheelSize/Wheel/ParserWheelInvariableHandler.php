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


use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelBackspacingClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelBarClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelDiameterClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelOffsetRangeClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelProfileClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelRimClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheelTireWeightClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelWheel\CarModelWheeWidthClassCheckerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserWheelInvariableHandler
{
    protected LoggerInterface $logger;

    /* Протокол и домен парсинга */
    private const WHEEL_SIZE_URI = 'https://www.wheel-size.com';

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
        MessageDispatchInterface $messageDispatch,
        private CarModelWheelClassCheckerService $carModelWheelClassCheckerService,
        private CarModelWheelDiameterClassCheckerService $carModelWheelDiameterClassCheckerService,
        private CarModelWheeWidthClassCheckerService $carModelWheelWidthClassCheckerService,
        private CarModelWheelProfileClassCheckerService $carModelWheelProfileClassCheckerService,
        private CarModelWheelOffsetRangeClassCheckerService $carModelWheelOffsetRangeClassCheckerService,
        private CarModelWheelBackspacingClassCheckerService $carModelWheelBackspacingClassCheckerService,
        private CarModelWheelRimClassCheckerService $carModelWheelRimClassCheckerService,
        private CarModelWheelTireWeightClassCheckerService $carModelWheelTireWeightClassCheckerService,
        private CarModelWheelBarClassCheckerService $carModelWheelBarClassCheckerService,
    )
    {
        $this->logger = $logger;
        $this->messageDispatch = $messageDispatch;
        $this->carModelWheelClassCheckerService = $carModelWheelClassCheckerService;
        $this->carModelWheelDiameterClassCheckerService = $carModelWheelDiameterClassCheckerService;
        $this->carModelWheelWidthClassCheckerService = $carModelWheelWidthClassCheckerService;
        $this->carModelWheelProfileClassCheckerService = $carModelWheelProfileClassCheckerService;
        $this->carModelWheelBackspacingClassCheckerService = $carModelWheelBackspacingClassCheckerService;
        $this->carModelWheelRimClassCheckerService = $carModelWheelRimClassCheckerService;
        $this->carModelWheelTireWeightClassCheckerService = $carModelWheelTireWeightClassCheckerService;
        $this->carModelWheelBarClassCheckerService = $carModelWheelBarClassCheckerService;
    }

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

        $tireParts = explode(' ', $tire);
        $result = [
            'originalEquipment' => null,
            'tireSize' => null,
            'loadIndex' => null,
        ];
        $result['originalEquipment'] = isset($tireParts[0]) && strtoupper($tireParts[0]) === 'OE';
        $result['tireSize'] = isset($tireParts[1]) ? $tireParts[1] : null;
        $result['loadIndex'] = isset($tireParts[2]) ? $tireParts[2] : null;

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

        //        if($message->getTire() == "OE 102V"){
        //            dd($sizePart);
        //        }

        if($sizePart !== false)
        {
            /**
             * Разбиваем параметры шин на переменные
             */
            $pattern = '/^(\d{3})\/(\d{2})([Z]?)([A-Za-z]?)(\d{2})$/';
            preg_match($pattern, $sizePart, $matches);

            $wheelParam = [
                'width' => (int) $matches[1],       // Ширина в мм
                'profile' => (int) $matches[2], // Высота профиля
                'zr_flag' => $matches[3],           // Флаг ZR (Z или пусто)
                'construction' => $matches[4] ?: 'R', // Тип конструкции (R по умолчанию)
                'rim_diameter' => (int) $matches[5],  // Диаметр диска в дюймах
            ];

            // Проверяем есть ли класс диаметра. Если нет, то генерируем
            $this->carModelWheelClassCheckerService->checkCarModelWheel($message);

            // Проверяем есть ли класс значения диаметра. Если нет, то генерируем
            $this->carModelWheelDiameterClassCheckerService->checkCarModelWheelDiameter($message, $wheelParam['rim_diameter']);

            // Проверяем есть ли класс ширины. Если нет, то генерируем
            $this->carModelWheelWidthClassCheckerService->checkCarModelWheelWidth($message, $wheelParam['width']);

            // Проверяем есть ли класс высоты профиля. Если нет, то генерируем
            $this->carModelWheelProfileClassCheckerService->checkCarModelWheelProfile($message, $wheelParam['profile']);

            // Проверяем есть ли класс Диапазона смещения. Если нет, то генерируем
            $this->carModelWheelOffsetRangeClassCheckerService->checkCarModelWheelOffsetRange($message);

            // Проверяем есть ли класс Backspacing. Если нет, то генерируем
            $this->carModelWheelBackspacingClassCheckerService->checkCarModelWheelBackspacing($message);

            // Проверяем есть ли класс Обода. Если нет, то генерируем
            $this->carModelWheelRimClassCheckerService->checkCarModelWheelRim($message);

            // Проверяем есть ли класс Обода. Если нет, то генерируем
            $this->carModelWheelTireWeightClassCheckerService->checkCarModelWheelTireWeight($message);

            // Проверяем есть ли класс Давления. Если нет, то генерируем
            $this->carModelWheelBarClassCheckerService->checkCarModelWheelBar($message);

        }
    }
}
