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

namespace BaksDev\Reference\Car\Service\CarModel;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Generator\CarModel\CarModelClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelClassCheckerService
{
    private LoggerInterface $logger;

    private const NAMESPACE = [
        "Type",
        "CarModels",
        "Id",
        "Models",
        "Collection",
    ];

    private string $collectionPath;

    private string $modelFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
    )
    {
        $this->logger = $logger;
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->modelFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли основной класс модели
     */
    public function checkModel($data): void
    {
        $this->logger->info('Проверка наличия основного класса модели: '.$data->getTitle());

        /**
         * Создаем полный физ путь для создания или проверки наличия папки
         */
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
        ]);

        /**
         * Если папки нет, то создаем
         */
        if(!is_dir($collectionPath))
        {
            $this->logger->info('Папки для основного класса модели: '.$data->getTitle().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        /**
         * Создаем полный namespace для основного класса модели
         */
        $modelFullNamespace = implode('\\', [
            $this->modelFullNamespace,
            $data->getClassName(),
        ]);

        if(!class_exists($modelFullNamespace))
        {
            $this->logger->info('Класс для основного класса модели: '.$data->getTitle().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $modelFullNamespace);
        }
        else
        {
            $this->logger->info('Основной класс модели: '.$modelFullNamespace.' существует.');
        }
    }

    /**
     * Генерирует класс
     *
     * @param $data
     *
     * @return void
     */
    public function generateClass($data, $collectionPath, $modelFullNamespace): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/'.$data->getClassName().'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelClassTemplate::getTemplate($data);
        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/'.$data->getClassName().'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($modelFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Основной класс для '.$data->getTitle().' создан');
    }
}
