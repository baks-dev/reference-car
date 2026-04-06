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

namespace BaksDev\Reference\Car\Service\CarModelGeneration;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Generator\CarModelGeneration\CarModelGenerationNameClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelGenerationNameClassCheckerService
{
    private LoggerInterface $logger;

    private const NAMESPACE = [
        "Type",
        "CarModelGenerations",
        "Name",
        "ModelGenerations",
        "Collection",
    ];

    private string $collectionPath;

    private string $generationNameFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
    )
    {
        $this->logger = $logger;
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->generationNameFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс
     */
    public function checkGenerationName($data): void
    {
        $this->logger->info('Проверка наличия класса названия поколения: '.$data->getTitle());

        /**
         * Проверяем равно ли значение названия класса модели и поколения
         * Если равны, то поколения нет и его не генерируем
         */
        $isGeneration = $data->getModel()['class_name'] === $data->getClassName() ? false : true;

        if($isGeneration === true)
        {
            // Создаем полный физ путь для создания или проверки наличия папки
            $collectionPath = $this->collectionPath;

            // Если папки нет, то создаем
            if(!is_dir($collectionPath))
            {
                $this->logger->info('Папки для класса названия поколения: '.$data->getTitle().' нет. Создаем папку');
                mkdir($collectionPath, 0755, true);
            }

            $generationNameFullNamespace = implode('\\', [
                $this->generationNameFullNamespace,
                $data->getClassName(),
            ]);

            if(!class_exists($generationNameFullNamespace))
            {
                $this->logger->info('Класс названия поколения: '.$data->getTitle().' отсутствует. Создаем класс');
                $this->generateClass($data, $collectionPath, $generationNameFullNamespace);
            }
            else
            {
                $this->logger->info('Класс названия поколения: '.$generationNameFullNamespace.' существует.');
            }
        }
    }

    /**
     * Генерирует класс
     *
     * @param $data
     *
     * @return void
     */
    public function generateClass($data, $collectionPath, $generationNameFullNamespace): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/'.$data->getClassName().'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelGenerationNameClassTemplate::getTemplate($data);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/'.$data->getClassName().'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($generationNameFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс названия поколения для '.$data->getTitle().' создан');
    }
}
