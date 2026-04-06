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

namespace BaksDev\Reference\Car\Service\CarModelPetrol;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolPowerClassCheckerServices\CarModelPetrolPowerHPClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolPowerClassCheckerServices\CarModelPetrolPowerKWClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolPowerClassCheckerServices\CarModelPetrolPowerPSClassCheckerService;
use Psr\Log\LoggerInterface;

class CarModelPetrolPowerClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelPetrols",
        "Id",
        "ModelPetrols",
        "Collection",
    ];

    private string $collectionPath;

    private string $modelPetrolNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private CarModelPetrolPowerHPClassCheckerService $modelPetrolPowerHPClassCheckerService,
        private CarModelPetrolPowerKWClassCheckerService $modelPetrolPowerKWClassCheckerService,
        private CarModelPetrolPowerPSClassCheckerService $modelPetrolPowerPSClassCheckerService,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->modelPetrolNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли классы мощности двигателя комплектации модели
     */
    public function checkModelPetrolPowerPetrol($data): void
    {
        $this->logger->info('Проверка наличия классов ModelPetrolPower: '.$data->getTitle());

        /**
         * Получаем параметры из строки
         */
        $modelPetrolPower = explode('|', $data->getPower());
        $hpParam = trim(array_filter($modelPetrolPower, function($item) {
            return stripos($item, 'HP') !== false;
        })[0]);
        $kwParam = trim(array_filter($modelPetrolPower, function($item) {
            return stripos($item, 'KW') !== false;
        })[1]);
        $psParam = trim(array_filter($modelPetrolPower, function($item) {
            return stripos($item, 'PS') !== false;
        })[2]);

        $this->modelPetrolPowerHPClassCheckerService->checkModelPetrolPowerHPPetrol($data, $hpParam);
        $this->modelPetrolPowerKWClassCheckerService->checkModelPetrolPowerKWPetrol($data, $kwParam);
        $this->modelPetrolPowerPSClassCheckerService->checkModelPetrolPowerPSPetrol($data, $psParam);
    }
}
