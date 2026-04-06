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

namespace BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolPowerClassCheckerServices;

use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Generator\CarModelPetrol\CarModelPetrolPowerClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelPetrolPowerHPClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelPetrols",
        "HP",
        "ModelPetrols",
        "Collection",
    ];

    private string $collectionPath;

    private string $modelPetrolPowerHPFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->modelPetrolPowerHPFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли классы мощности двигателя комплектации модели в hp
     */
    public function checkModelPetrolPowerHPPetrol($data, $hpParam): void
    {
        $this->logger->info('Проверка наличия классов ModelPetrolPowerHP: '.$data->getTitle());

        $hpParamClassName = strtoupper(str_replace([' ', '.'], '', $hpParam));

        $modelClassName = $data->getGeneration()['model']['class_name'];

        // Создаем полный физ путь для создания или проверки наличия папки
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
            $modelClassName,
            'Petrol'.$data->getClassName().$hpParamClassName,
        ]);

        // Создаем полный namespace для класса комплектации модели
        $modelPetrolPowerHPFullNamespace = implode('\\', [
            $this->modelPetrolPowerHPFullNamespace,
            $modelClassName,
            'Petrol'.$data->getClassName().$hpParamClassName,
            'Power'.$hpParamClassName]);

        if(!class_exists($modelPetrolPowerHPFullNamespace))
        {
            $this->logger->info('Класс Power'.$hpParamClassName.': '.$data->getTitle().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $hpParam, $hpParamClassName, $modelPetrolPowerHPFullNamespace);
        }
        else
        {
            $this->logger->info('Класс Power'.$hpParamClassName.': '.$modelPetrolPowerHPFullNamespace.' существует.');
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
        $hpParam,
        $hpParamClassName,
        $modelPetrolPowerHPFullNamespace
    ): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/Power'.$hpParamClassName.'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelPetrolPowerClassTemplate::getTemplate($data, $hpParam, $hpParamClassName);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/Power'.$hpParamClassName.'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($modelPetrolPowerHPFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс Power'.$hpParamClassName.' для '.$data->getTitle().' создан');
    }
}
