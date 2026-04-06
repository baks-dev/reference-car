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

namespace BaksDev\Reference\Car\Service\CarModel\CarModelPetrolPowerClassCheckerServices;

use BaksDev\Reference\Car\Generator\CarModelPetrol\CarModelPetrolPowerClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelPetrolPowerKWClassCheckerService
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    ) {}

    /**
     * Проверяет есть ли классы мощности двигателя комплектации модели в hp
     */
    public function checkModelPetrolPowerKWPetrol($data, $collectionPath, $kwParam, $modelPetrolNamespace): void
    {
        $this->logger->info('Проверка наличия классов ModelPetrolPowerKW: '.$data->getTitle());

        $kwParamClassName = strtoupper(str_replace([' ', '.'], '', $kwParam));

        // Создаем полный namespace для класса комплектации модели
        $modelPetrolPowerKWFullNamespace = $modelPetrolNamespace.'\\Power'.$kwParamClassName;

        if(!class_exists($modelPetrolPowerKWFullNamespace))
        {
            $this->logger->info('Класс Power'.$kwParamClassName.': '.$data->getTitle().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $kwParam, $kwParamClassName, $modelPetrolPowerKWFullNamespace);
        }
        else
        {
            $this->logger->info('Класс Power'.$kwParamClassName.': '.$modelPetrolPowerKWFullNamespace.' существует.');
        }
    }

    /**
     * Генерирует класс
     *
     * @param $data
     *
     * @return void
     */
    public function generateClass(
        $data,
        $collectionPath,
        $kwParam,
        $kwParamClassName,
        $modelPetrolPowerKWFullNamespace
    ): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/Power'.$kwParamClassName.'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelPetrolPowerClassTemplate::getTemplate($data, $kwParam, $kwParamClassName);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/Power'.$kwParamClassName.'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($modelPetrolPowerKWFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс Power'.$kwParamClassName.' для '.$data->getTitle().' создан');
    }
}
