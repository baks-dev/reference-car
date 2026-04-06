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
use BaksDev\Reference\Car\Generator\CarModelWheel\CarModelWheelClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelWheelClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelWheels",
        "Id",
        "ModelWheels",
        "Collection",
    ];

    private string $collectionPath;

    private string $carModelWheelFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->carModelWheelFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс колеса
     */
    public function checkCarModelWheel($data): void
    {
        $this->logger->info('Проверка наличия класса колеса: '.$data->getTire());

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
            $this->logger->info('Папки для класса колеса: '.$data->getTire().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        /**
         * Создаем полный namespace для основного класса колеса
         */
        $carModelWheelFullNamespace = implode('\\', [
            $this->carModelWheelFullNamespace,
            $data->getGeneration()['model']['class_name'],
            $data->getClassName(),
        ]);

        if(!class_exists($carModelWheelFullNamespace))
        {
            $this->logger->info('Класс колеса: '.$data->getTire().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $carModelWheelFullNamespace);
        }
        else
        {
            $this->logger->info('Класс колеса: '.$carModelWheelFullNamespace.' существует.');
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
    public function generateClass($data, $collectionPath, $carModelWheelFullNamespace): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/'.$data->getClassName().'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelWheelClassTemplate::getTemplate($data);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/'.$data->getClassName().'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($carModelWheelFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс колеса для '.$data->getTire().' создан');
    }
}
