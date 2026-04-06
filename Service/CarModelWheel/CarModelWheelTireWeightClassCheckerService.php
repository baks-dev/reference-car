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

namespace BaksDev\Reference\Car\Service\CarModelWheel;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Generator\CarModelWheel\CarModelWheelTireWeightClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelWheelTireWeightClassCheckerService
{
    private LoggerInterface $logger;

    private const NAMESPACE = [
        "Type",
        "CarModelWheels",
        "TireWeight",
        "ModelWheels",
        "Collection",
    ];

    private string $collectionPath;

    private string $carModelWheelTireWeightFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
    )
    {
        $this->logger = $logger;
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->carModelWheelTireWeightFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс TireWeight
     */
    public function checkCarModelWheelTireWeight($data): void
    {
        if($data->getTireWeight() == null)
        {
            return;
        }

        $this->logger->info('Проверка наличия класса Вес шины колеса: '.$data->getTire());
        /**
         * Создаем полный физ путь для создания или проверки наличия папки
         */
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
            $data->getGeneration()['model']['class_name'],
        ]);

        // Если папки нет, то создаем
        if(!is_dir($collectionPath))
        {
            $this->logger->info('Папки для класса значения Вес шины колеса: '.$data->getTire().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        /**
         * Создаем полный namespace для основного класса модели
         */
        $carModelWheelTireWeightFullNamespace = implode('\\', [
            $this->carModelWheelTireWeightFullNamespace,
            $data->getGeneration()['model']['class_name'],
            $data->getClassName(),
        ]);

        if(!class_exists($carModelWheelTireWeightFullNamespace))
        {
            $this->logger->info('Класс значения Вес шины колеса: '.$data->getTire().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $carModelWheelTireWeightFullNamespace);
        }
        else
        {
            $this->logger->info('Класс значения Вес шины колеса: '.$carModelWheelTireWeightFullNamespace.' существует.');
        }

        $delay = 1000000; // 1 секунда в микросекундах
        usleep($delay);
    }

    /**
     * Генерирует класс
     *
     * @param $data
     *
     * @return void
     */
    public function generateClass($data, $collectionPath, $carModelWheelTireWeightFullNamespace): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/'.$data->getClassName().'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelWheelTireWeightClassTemplate::getTemplate($data);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/'.$data->getClassName().'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($carModelWheelTireWeightFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс значения Вес шины колеса для '.$data->getTire().' создан');
    }
}
