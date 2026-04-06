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
use BaksDev\Reference\Car\Generator\CarModelPetrol\CarModelPetrolSaleRegionClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelPetrolSaleRegionClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelPetrols",
        "SaleRegion",
        "ModelPetrols",
        "Collection",
    ];

    private string $collectionPath;

    private string $carModelPetrolSaleRegionFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->carModelPetrolSaleRegionFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс года комплектации модели
     */
    public function checkCarModelPetrolSaleRegion($data): void
    {
        $this->logger->info('Проверка наличия класса ModelPetrolSaleRegion: '.$data->getTitle());

        $modelClassSaleRegion = $data->getGeneration()['model']['class_name'];
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );

        // Создаем полный физ путь для создания или проверки наличия папки
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
            $modelClassSaleRegion,
            'Petrol'.$data->getClassName().$enginePowerClassName,
        ]);

        // Если папки нет, то создаем
        if(!is_dir($collectionPath))
        {
            $this->logger->info('Папки для ModelPetrolSaleRegion: '.$data->getTitle().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        /**
         * Формируем Страны из строк
         */
        $salesRegions = $data->getSalesRegions();

        // Разбиваем строку на массив и обрабатываем каждый элемент
        $salesRegions = array_map(function($item) {
            // Удаляем лишние пробелы и символы '+'
            $value = trim(str_replace('+', '', $item));

            // Создаем class_name - удаляем все пробелы
            $className = str_replace(' ', '', $value);

            return [
                'value' => $value,
                'class_name' => $className,
            ];
        }, explode(',', $salesRegions));

        /**
         * Для каждой страны создаем класс
         */
        foreach($salesRegions as $saleRegion)
        {
            // Создаем полный namespace для класса комплектации модели
            $carModelPetrolSaleRegionFullNamespace = implode('\\', [
                $this->carModelPetrolSaleRegionFullNamespace,
                $data->getGeneration()['model']['class_name'],
                'Petrol'.$data->getClassName().$enginePowerClassName,
                'ModelPetrolSaleRegion'.$saleRegion['class_name'],
            ]);

            if(!class_exists($carModelPetrolSaleRegionFullNamespace))
            {
                $this->logger->info('Класс для года комплектации : '.$data->getTitle().' отсутствует. Создаем класс');
                $this->generateClass($data, $collectionPath, $carModelPetrolSaleRegionFullNamespace, $saleRegion);
            }
            else
            {
                $this->logger->info('Класс ModelPetrolSaleRegion: '.$carModelPetrolSaleRegionFullNamespace.' существует.');
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
    public function generateClass(
        $data,
        $collectionPath,
        $carModelPetrolSaleRegionFullNamespace,
        $saleRegion
    ): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/ModelPetrolSaleRegion'.$saleRegion['class_name'].'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelPetrolSaleRegionClassTemplate::getTemplate($data, $saleRegion);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/ModelPetrolSaleRegion'.$saleRegion['class_name'].'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($carModelPetrolSaleRegionFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс ModelPetrolSaleRegion '.$data->getTitle().' создан');
    }
}
