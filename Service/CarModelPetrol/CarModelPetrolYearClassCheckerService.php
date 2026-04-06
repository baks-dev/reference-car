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
use BaksDev\Reference\Car\Generator\CarModelPetrol\CarModelPetrolYearClassTemplate;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Filesystem\Filesystem;

class CarModelPetrolYearClassCheckerService
{
    private const NAMESPACE = [
        "Type",
        "CarModelPetrols",
        "Year",
        "ModelPetrols",
        "Collection",
    ];

    private string $collectionPath;

    private string $carModelPetrolYearFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
    )
    {
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->carModelPetrolYearFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Проверяет есть ли класс года комплектации модели
     */
    public function checkCarModelPetrolYear($data): void
    {
        $this->logger->info('Проверка наличия класса ModelPetrolYear: '.$data->getTitle());

        $modelClassYear = $data->getGeneration()['model']['class_name'];
        $enginePowerClassName = strtoupper(
            str_replace(' ', '', (
            explode('|', $data->getAll()['power'])[0]
            ),
            ),
        );

        // Создаем полный физ путь для создания или проверки наличия папки
        $collectionPath = implode(DIRECTORY_SEPARATOR, [
            $this->collectionPath,
            $modelClassYear,
            'Petrol'.$data->getClassName().$enginePowerClassName,
        ]);

        // Если папки нет, то создаем
        if(!is_dir($collectionPath))
        {
            $this->logger->info('Папки для ModelPetrolName: '.$data->getTitle().' нет. Создаем папку');
            mkdir($collectionPath, 0755, true);
        }

        /**
         * Формируем года из строк
         */
        $years = $this->parseYearRange($data->getProduction());

        /**
         * Для каждого года создаем класс
         */
        foreach($years as $year)
        {
            // Создаем полный namespace для класса комплектации модели
            $carModelPetrolYearFullNamespace = implode('\\', [
                $this->carModelPetrolYearFullNamespace,
                $modelClassYear,
                'Petrol'.$data->getClassName().$enginePowerClassName,
                'ModelPetrolYear'.$year]);

            if(!class_exists($carModelPetrolYearFullNamespace))
            {
                $this->logger->info('Класс для года комплектации : '.$data->getTitle().' отсутствует. Создаем класс');
                $this->generateClass($data, $collectionPath, $carModelPetrolYearFullNamespace, $year);
            }
            else
            {
                $this->logger->info('Класс ModelPetrolYear: '.$carModelPetrolYearFullNamespace.' существует.');
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
        $carModelPetrolYearFullNamespace,
        $year
    ): void
    {
        $filesystem = new Filesystem();
        $filePath = $collectionPath.'/ModelPetrolYear'.$year.'.php';

        // Получает сгенерированное содержание класса
        $classContent = CarModelPetrolYearClassTemplate::getTemplate($data, $year);

        // Создает класс с сгенерированным содержанием
        $filesystem->dumpFile($collectionPath.'/ModelPetrolYear'.$year.'.php', $classContent);

        // Скидываем кеш после создания класса
        clearstatcache(true, $filePath);
        if(function_exists('opcache_invalidate'))
        {
            opcache_invalidate($filePath, true);
        }

        //Явно загружаем класс
        if(!class_exists($carModelPetrolYearFullNamespace, false))
        {
            include $filePath;
        }

        $this->logger->info('Класс ModelPetrolYear '.$data->getTitle().' создан');
    }

    /**
     * Из строк с годами создает массив годов
     *
     * @param $rangeString
     *
     * @return array
     */
    public function parseYearRange($rangeString)
    {
        // Удаляем квадратные скобки и пробелы
        $cleaned = trim($rangeString, '[] ');

        // Разбиваем строку по разделителю ".."
        $parts = explode('..', $cleaned);

        // Удаляем возможные пробелы вокруг чисел
        $startYear = trim($parts[0]);
        $endYear = trim($parts[1]);

        // Преобразуем в числа
        $start = (int) $startYear;
        $end = (int) $endYear;

        // Генерируем массив годов
        $years = [];
        for($year = $start; $year <= $end; $year++)
        {
            $years[] = $year;
        }

        return $years;
    }
}
