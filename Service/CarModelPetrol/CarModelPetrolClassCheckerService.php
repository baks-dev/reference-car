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
use BaksDev\Reference\Car\Generator\CarModelPetrol\CarModelPetrolClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelPetrolClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelPetrols",
        "Id",
        "ModelPetrols",
        "Collection",
    ];

    private string $collectionPath;

    private string $modelPetrolFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->modelPetrolFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс комплектации модели
     */
    public function checkModelPetrol($data): void
    {
        $this->logger->info('Проверка наличия класса ModelPetrol: '.$data->getTitle());

        $modelClassName = $data->getGeneration()['model']['class_name'];
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );

        // Создаем полный физ путь для создания или проверки наличия папки
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
            $modelClassName,
            'Petrol'.$data->getClassName().$enginePowerClassName,
        ]);

        // Если папки нет, то создаем
        if(!is_dir($collectionPath))
        {
            $this->logger->info('Папки для ModelPetrol: '.$data->getTitle().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        // Создаем полный namespace для класса комплектации модели
        $modelPetrolFullNamespace = implode('\\', [
            $this->modelPetrolFullNamespace,
            $modelClassName,
            'Petrol'.$data->getClassName().$enginePowerClassName,
            'ModelPetrol']);

        if(!class_exists($modelPetrolFullNamespace))
        {
            $this->logger->info('Класс для основного класса модели: '.$data->getTitle().' отсутствует. Создаем класс');
            $this->generateClass($data, $collectionPath, $modelPetrolFullNamespace);
        }
        else
        {
            $this->logger->info('Класс ModelPetrol: '.$modelPetrolFullNamespace.' существует.');
        }
    }

    /**
     * Генерирует класс
     *
     * @param $data
     *
     * @return void
     */
    public function generateClass($data, $collectionPath, $modelPetrolFullNamespace): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/ModelPetrol.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelPetrolClassTemplate::getTemplate($data);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/ModelPetrol.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($modelPetrolFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс ModelPetrol '.$data->getTitle().' создан');
    }
}
