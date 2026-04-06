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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarModelGeneration;


use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Messenger\WheelSize\CarEquipment\ParserCarEquipmentMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\Wheel\ParserWheelMessage;
use BaksDev\Reference\Car\Service\CarModelGeneration\CarModelGenerationClassCheckerService;
use BaksDev\Reference\Car\Service\CarModelGeneration\CarModelGenerationNameClassCheckerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserCarModelGenerationInvariableHandler
{
    /* Протокол и домен парсинга */
    private const WHEEL_SIZE_URI = 'https://www.wheel-size.com';

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private MessageDispatchInterface $messageDispatch,
        private CarModelGenerationClassCheckerService $carModelGenerationClassCheckerService,
        private CarModelGenerationNameClassCheckerService $carModelGenerationNameClassCheckerService,
        private ParserCarModelGenerationRequest $parserCarModelGenerationRequest
    )
    {
        $this->logger = $logger;
        $this->messageDispatch = $messageDispatch;
        $this->carModelGenerationClassCheckerService = $carModelGenerationClassCheckerService;
        $this->carModelGenerationNameClassCheckerService = $carModelGenerationNameClassCheckerService;
        $this->parserCarModelGenerationRequest = $parserCarModelGenerationRequest;
    }

    public function __invoke(ParserCarModelGenerationMessage $message): void
    {
        // Проверяем есть ли поколения модели. Если нет, то генерируем
        $this->carModelGenerationClassCheckerService->checkModelGeneration($message);

        // Проверяем есть ли класс названия поколения. Если нет, то генерируем
        $this->carModelGenerationNameClassCheckerService->checkGenerationName($message);

        // Парсим внутреннюю страницу поколения
        $this->parseGeneration($message);
    }

    /**
     * Парсит внутренние страницы поколений
     *
     * @param ParserCarModelGenerationMessage $generation
     *
     * @return void
     */
    private function parseGeneration(ParserCarModelGenerationMessage $carModelGeneration)
    {
        $carModelGeneration = $carModelGeneration->getAll();

        $this->logger->info('Начинаем парсить поколение '.$carModelGeneration['title'].'с url: '.$carModelGeneration['href']);

        // Получаем HTML с внутренней страницы поколения
        $html = $this->parserCarModelGenerationRequest->fetchHtml($carModelGeneration['href']);
        $crawler = new Crawler($html, self::WHEEL_SIZE_URI.$carModelGeneration['href']);

        // Ищет список комплектаций
        $equipmentsGroup = $crawler->filter('.region-trim');

        // Если комплектации найдены, то начинает получать значения с HTML
        if($equipmentsGroup->count() > 0)
        {
            $equipments = $equipmentsGroup->each(function(Crawler $equipment): array {
                $trimData = [
                    'model_name' => trim($equipment->filter('h4 .hidden-sm-down')->text()),
                    'equipment_name' => trim($equipment->filter('h4 .panel-hdr-trim-name')->text()),
                    // 'generation' => trim(str_replace('Generation:', '', $equipment->filter('.parameter-list-left .element-parameter:nth-child(1)')->text())),
                    'production' => trim(str_replace('Production:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Production:")]')->text())),
                    'sales_regions' => trim(str_replace('Sales regions:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Sales regions:")]')->text())),
                    'power' => trim(str_replace('Power:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Power:")]')->text())),
                    'engine' => trim(str_replace('Engine:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Engine:")]')->text())),
                    'center_bore' => trim(str_replace('Center Bore / Hub Bore:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Center Bore / Hub Bore:")]')->text())),
                    'pcd' => trim(str_replace('Bolt Pattern (PCD):', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Bolt Pattern (PCD):")]')->text())),
                    'wheel_fasteners' => trim(str_replace('Wheel Fasteners:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Wheel Fasteners:")]')->text())),
                    'thread_size' => trim(str_replace('Thread Size:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Thread Size:")]')->text())),
                    'wheel_tightening_torque' => trim(str_replace('Wheel Tightening Torque:', '', $equipment->filterXPath('//*[contains(@class, "element-parameter") and contains(., "Wheel Tightening Torque:")]')->text())),
                ];
                $trimData['sales_regions'] = str_replace(
                    [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                    '',
                    $trimData['sales_regions'],
                );

                // Получаем данные колес от комплектации
                $trimData['wheels'] = $equipment->filter('tr.stock')->each(function(Crawler $row) use ($trimData
                ): array {

                    /**
                     * Получаем значения шин
                     */
                    $fullTireText = $row->filter('td.data-tire')->count() ?
                        $row->filter('td.data-tire')->text() : '';
                    $secondTireText = $row->filter('td.data-tire .rear-tire-data')->count() ?
                        $row->filter('td.data-tire .rear-tire-data')->text() : '';
                    $firstTireText = trim(str_replace($secondTireText, '', $fullTireText));

                    /**
                     * Получаем значения шин RIM
                     */
                    $fullRimText = $row->filter('td.data-rim')->count() ?
                        $row->filter('td.data-rim')->text() : '';
                    $secondRimText = $row->filter('td.data-rim .rear-rim-data')->count() ?
                        $row->filter('td.data-rim .rear-rim-data')->text() : '';
                    $firstRimText = trim(str_replace($secondRimText, '', $fullRimText));

                    /**
                     * Получаем значения шин Offset Range
                     */
                    $fullOffsetRangeText = $row->filter('td.data-offset-range')->count() ?
                        $row->filter('td.data-offset-range')->text() : '';
                    $secondOffsetRangeText = $row->filter('td.data-offset-range .d-block')->count() ?
                        $row->filter('td.data-offset-range .d-block')->text() : '';
                    $firstOffsetRangeText = trim(str_replace($secondOffsetRangeText, '', $fullOffsetRangeText));

                    /**
                     * Получаем значения шин Backspacing
                     */
                    $fullBackspacingText = $row->filter('td.data-backspacing .metric')->count() ?
                        $row->filter('td.data-backspacing .metric')->text() : '';
                    $firstBackspacingText = substr($fullBackspacingText, 0, 3);  // Первые 3 символа
                    $secondBackspacingText = substr($fullBackspacingText, 3, 3); // Следующие 3 символа

                    /**
                     * Получаем значения шин Tire Weight
                     */
                    $fullTireWeightText = $row->filter('td.data-weight .metric')->count() ?
                        $row->filter('td.data-weight .metric')->text() : '';
                    $firstTireWeightText = substr($fullTireWeightText, 0, 4);  // Первые 4 символа
                    $secondTireWeightText = substr($fullTireWeightText, 4, 4); // Следующие 4 символа

                    /**
                     * Получаем значения шин Bar
                     */
                    $fullBarText = $row->filter('td.data-pressure .metric')->count() ?
                        $row->filter('td.data-pressure .metric')->text() : '';
                    $firstBarText = substr($fullBarText, 0, 3);  // Первые 3 символа
                    $secondBarText = substr($fullBarText, 3, 3); // Следующие 3 символа


                    return [
                        'tire' => [$firstTireText, $secondTireText],
                        'rim' => [$firstRimText, $secondRimText],
                        'offset_range' => [$firstOffsetRangeText, $secondOffsetRangeText],
                        'backspacing' => [$firstBackspacingText, $secondBackspacingText],
                        'tire_weight' => [$firstTireWeightText, $secondTireWeightText],
                        'bar' => [$firstBarText, $secondBarText],
                    ];
                });

                return $trimData;
            });

            foreach($equipments as $equipment)
            {
                $equipment['title'] = $equipment['model_name'].' '.$equipment['equipment_name'];

                // Составляем имя класса поколения
                $equipment['class_name'] = preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $equipment['equipment_name'],
                    ),
                );
                $equipment['generation'] = $carModelGeneration;

                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    new ParserCarEquipmentMessage(
                        (string) $carModelGeneration['href'],
                        (string) $equipment['class_name'],
                        (string) $equipment['equipment_name'],
                        (string) $equipment['production'],
                        (string) $equipment['sales_regions'],
                        (string) $equipment['power'],
                        (string) $equipment['engine'],
                        (string) $equipment['center_bore'],
                        (string) $equipment['pcd'],
                        (string) $equipment['wheel_fasteners'],
                        (string) $equipment['thread_size'],
                        (string) $equipment['wheel_tightening_torque'],
                        (string) $equipment['title'],
                        (array) $equipment['generation'],
                    ),
                //                    stamps: [new MessageDelay('5 seconds')],
                //                    transport: (string) 'reference-car'
                );

                foreach($equipment['wheels'] as $wheel)
                {

                    /**
                     * Разделяем каждый массив на передние и задние колеса
                     */
                    $wheelFront = array_map(fn($v) => $v[0] ?? null, $wheel);
                    $wheelRear = array_map(fn($v) => $v[1] ?? null, $wheel);

                    foreach([$wheelFront, $wheelRear] as $wheelData)
                    {
                        if(empty($wheelData['tire']))
                        {
                            continue;
                        }

                        // Составляем имя класса шины
                        $wheelData['class_name'] = preg_replace(
                            '/[^a-zA-Z0-9]/',
                            '',
                            str_replace(
                                [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                                '',
                                $wheelData['tire'],
                            ),
                        );
                        $wheelData['generation'] = $carModelGeneration;

                        /**
                         * Создаем имя папки связанной model petrol
                         */

                        // Удаляем все пробелы и разбиваем по |
                        $parts = explode('|', str_replace(' ', '', $equipment['power']));
                        // Находим индекс элемента с "hp" и преобразуем его
                        $hpIndex = array_search('hp', array_map(fn($p
                        ) => substr($p, strpos($p, 'hp') ?: -2, 2), $parts));
                        $relatedModelPetrolDirName = 'Petrol'.$equipment['class_name'].strtoupper($parts[$hpIndex]);

                        // Отправляяем данные в очередь
                        $this->messageDispatch->dispatch(
                            new ParserWheelMessage(
                                (string) $wheelData['class_name'],
                                (string) $relatedModelPetrolDirName,
                                (string) $wheelData['tire'],
                                (string) $wheelData['rim'],
                                (string) $wheelData['offset_range'],
                                (string) $wheelData['backspacing'],
                                (string) $wheelData['tire_weight'],
                                (string) $wheelData['bar'],
                                (array) $wheelData['generation'],
                            ),
                        //                            stamps: [new MessageDelay('5 seconds')],
                        //                            transport: (string) 'reference-car'
                        );
                    }
                }
            }
        }
    }
}
