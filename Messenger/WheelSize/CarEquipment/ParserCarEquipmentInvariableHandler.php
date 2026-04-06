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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarEquipment;

use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolNameClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolPowerClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolSaleRegionClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolYearClassCheckerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserCarEquipmentInvariableHandler
{
    protected LoggerInterface $logger;

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
        MessageDispatchInterface $messageDispatch,
        private CarModelPetrolClassCheckerService $carModelPetrolClassCheckerService,
        private CarModelPetrolNameClassCheckerService $carModelPetrolNameClassCheckerService,
        private CarModelPetrolPowerClassCheckerService $carModelPetrolPowerClassCheckerService,

        private CarModelPetrolSaleRegionClassCheckerService $carModelPetrolSaleRegionClassCheckerService,
        private CarModelPetrolYearClassCheckerService $carModelPetrolYearClassCheckerService,
    )
    {
        $this->logger = $logger;
        $this->messageDispatch = $messageDispatch;
    }

    public function __invoke(ParserCarEquipmentMessage $message): void
    {
        echo $message->getTitle().PHP_EOL;

        // Проверяем есть ли класс Model Petrol. Если нет, то генерируем
        $this->carModelPetrolClassCheckerService->checkModelPetrol($message);

        // Проверяем есть ли класс Model Petrol Name. Если нет, то генерируем
        $this->carModelPetrolNameClassCheckerService->checkModelPetrolName($message);

        // Проверяем есть ли классы параметров мощности. Если нет, то генерируем
        $this->carModelPetrolPowerClassCheckerService->checkModelPetrolPowerPetrol($message);

        // Проверяем есть ли классы годов комплектаций. Если нет, то генерируем
        $this->carModelPetrolYearClassCheckerService->checkCarModelPetrolYear($message);

        // Проверяем есть ли класс Центрального отверстия CenterBore. Если нет, то генерируем
        $this->carModelPetrolSaleRegionClassCheckerService->checkCarModelPetrolSaleRegion($message);

        //        $this->modelPetrolClassCheckerService->checkModelPetrol($message);
    }

}
