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
use Symfony\Component\Filesystem\Filesystem;

final class CarModelClassCheckerService
{
    /**
     * Проверяет есть ли основной класс модели
     */
    public function checkModel(CarModelClassCheckerDTO $data): void
    {
        echo 'Проверка наличия класса модели: '.$data->getTitle().PHP_EOL;

        $shortNamespace = str_replace(BaksDevReferenceCarBundle::NAMESPACE, '', $data->getNamespace());
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            str_replace('\\', DIRECTORY_SEPARATOR, $shortNamespace),
        ]);


        /**
         * Если папки для сохранения файла нет, то создаем
         */
        if(false === is_dir($collectionPath))
        {
            echo 'Папки для класса модели: '.$data->getTitle().' нет. Создаем папку'.PHP_EOL;
            mkdir($collectionPath, 0755, true);
        }


        /**
         * Создаем полный namespace для класса модели
         */
        $modelFullNamespace = $data->getNamespace().$data->getClassName();

        if(false === class_exists($modelFullNamespace))
        {
            echo 'Класс для класса модели: '.$data->getTitle().' отсутствует. Создаем класс'.PHP_EOL;
            $this->generateClass($data, $collectionPath, $modelFullNamespace);
        }
        else
        {
            echo 'Класс модели: '.$modelFullNamespace.' существует.'.PHP_EOL;
        }
    }


    /**
     * Генерирует класс
     */
    public function generateClass(
        CarModelClassCheckerDTO $data,
        string $collectionPath,
        string $modelFullNamespace
    ): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/'.$data->getClassName().'.php';


        // Получает сгенерированное содержание класса
        $classContent = CarModelClassTemplate::getTemplate($data);


        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($filePath, $classContent);


        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }


        // Явно загружаем класс
        if(!class_exists($modelFullNamespace, false))
        {
            include $filePath;
        }


        echo 'Основной класс для '.$data->getTitle().' создан'.PHP_EOL;
    }
}
