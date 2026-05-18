<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Messenger\WheelSize\Petrol;

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Command\Parser\RunParserWheelSizeCommand;
use BaksDev\Reference\Car\Messenger\WheelSize\Wheel\ParserWheelMessage;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolClassCheckerDTO;
use BaksDev\Reference\Car\Service\CarModelPetrol\CarModelPetrolClassCheckerService;
use BaksDev\Reference\Car\Type\CarModelPetrols\CarModelPetrol;
use DateInterval;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserCarPetrolInvariableHandler
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private DeduplicatorInterface $Deduplicator,
        private CarModelPetrolClassCheckerService $CarModelPetrolClassCheckerService,
        private ParserCarPetrolRequest $ParserCarPetrolRequest,
        private MessageDispatchInterface $MessageDispatch,
    ) {}

    public function __invoke(ParserCarPetrolMessage $message): void
    {
        // Парсим внутреннюю страницу поколения
        $this->parsePetrol($message);
    }


    /**
     * Парсит внутренние элементы комплектаций
     */
    private function parsePetrol(ParserCarPetrolMessage $message): void
    {
        if(false === $message->isForced())
        {
            /** Делаем проверку дедупликатором */
            $Deduplicator = $this->Deduplicator
                ->namespace('reference-car')
                ->expiresAfter(DateInterval::createFromDateString('1 day'))
                ->deduplication([$message->getClassName(), md5(self::class)]);

            if($Deduplicator->isExecuted())
            {
                return;
            }
        }

        echo 'Начинаем парсить комплектацию '.$message->getTitle().'с url: '.$message->getUrl().PHP_EOL;
        $this->logger->info(
            'Начинаем парсить поколение '.$message->getTitle().'с url: '.$message->getUrl(),
        );


        // Получаем HTML из контейнера с информацией о комплектации
        $html = $this->ParserCarPetrolRequest->fetchHtml($message->getUrl());
        $crawler = new Crawler($html, RunParserWheelSizeCommand::WHEEL_SIZE_URL.$message->getUrl());


        $hp = 0;
        $kw = 0;
        $ps = 0;
        $crawler
            ->filter('.ws-power-value')
            ->each(function(Crawler $node) use (&$hp, &$kw, &$ps)
            {
                if (str_contains($node->text(), 'hp') !== false) {
                    // Extract number for hp
                    if (preg_match('/\d+/', $node->text(), $matches)) {
                        $hp = (int) $matches[0];
                    }
                }

                if (str_contains($node->text(), 'kW') !== false) {
                    // Extract number for kW
                    if (preg_match('/\d+/', $node->text(), $matches)) {
                        $kw = (int) $matches[0];
                    }
                }

                if (str_contains($node->text(), 'PS') !== false) {
                    // Extract number for PS
                    if (preg_match('/\d+/', $node->text(), $matches)) {
                        $ps = (int) $matches[0];
                    }
                }
            });

        $years = $crawler
            ->filter('.masha_index7')
            ->closest('.element-parameter')
            ->text();

        $years = str_replace(' .. ', '-', trim(trim(explode(':', $years)[1]), '[]'));


        $CarModelPetrolClassCheckerDTO = new CarModelPetrolClassCheckerDTO(
            $message->getClassName(),
            $message->getTitle(),
            $hp,
            $kw,
            $ps,
            $years,
            $message->getGeneration()
        );


        // Проверяем есть ли класс комплектации. Если нет, то генерируем
        $this->CarModelPetrolClassCheckerService->checkModelPetrol($CarModelPetrolClassCheckerDTO);


        /** Находим список шин */
        $wheelGroup = $crawler->filter('tr.stock')->each(function(Crawler $row)
            {
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
            }
        );

        foreach($wheelGroup as $wheel)
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
                $className = $message->getClassName().preg_replace(
                        '/[^a-zA-Z0-9]/',
                        '',
                        str_replace(
                            [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                            '',
                            $wheelData['tire'],
                        ),
                    );


                $sleep = new Randomizer()->getInt(3, 7);

                if(false === RunParserWheelSizeCommand::IS_ASYNC)
                {
                    sleep($sleep);
                }


                // Отправляяем данные в очередь
                $this->MessageDispatch->dispatch(new ParserWheelMessage(
                    $className,
                    (string) $wheelData['tire'],
                    (string) $wheelData['rim'],
                    (string) $wheelData['offset_range'],
                    (string) $wheelData['backspacing'],
                    (string) $wheelData['tire_weight'],
                    (string) $wheelData['bar'],
                    new CarModelPetrol($message->getClassName(), $message->getNamespace(), $message->getGeneration()),
                    $message->isForced()
                ));
            }
        }

        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }
}