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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarModel;


use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModelGeneration\ParserCarModelGenerationMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModelImage\ParserCarModelImageMessage;
use BaksDev\Reference\Car\Service\CarModel\CarModelClassCheckerService;
use BaksDev\Reference\Car\Service\CarModel\CarModelNameClassCheckerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserCarModelInvariableHandler
{
    /* Протокол и домен парсинга */
    private const WHEEL_SIZE_URI = 'https://www.wheel-size.com';

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private MessageDispatchInterface $messageDispatch,
        private CarModelClassCheckerService $carModelClassCheckerService,
        private CarModelNameClassCheckerService $carModelNameClassCheckerService,
        private ParserCarModelRequest $parserModelRequest
    ) {}

    public function __invoke(ParserCarModelMessage $message): void
    {
        // Проверяем есть ли класс модели. Если нет, то генерируем
        $this->carModelClassCheckerService->checkModel($message);

        // Проверяем есть ли класс названия модели. Если нет, то генерируем
        $this->carModelNameClassCheckerService->checkModelName($message);

        // Парсим внутреннюю страницу модели
        $this->parseModel($message);
    }

    /**
     * Парсит внутренние страницы моделей
     *
     * @param ParserCarModelMessage $model
     *
     * @return void
     */
    private function parseModel(ParserCarModelMessage $model): void
    {
        $carModelArray = $model->getAll();
        echo $carModelArray['title'].PHP_EOL;

        $this->logger->info('Начинаем парсить модель '.$carModelArray['title'].' по url: '.$carModelArray['href']);

        // Получаем HTML с внутренней страницы модели
        $html = $this->parserModelRequest->fetchHtml($carModelArray['href']);
        $crawler = new Crawler($html, self::WHEEL_SIZE_URI.$carModelArray['href']);

        // Ищет список поколений
        $carModelGenerationsGroup = $crawler->filter('.col-md-12.col-sm-12.col-lg-8 .mb-4');

        // Если поколения найдены, то начинает получать значения с HTML
        if($carModelGenerationsGroup->count() > 0)
        {
            $carModelGenerations = $carModelGenerationsGroup->filter('.market-generation')->each(function(Crawler $node
            ) use ($model) {

                $years = $node->filter('div.mb-4 a span span')->each(function(Crawler $yearNode) {
                    return $yearNode->text();
                });
                $countries = $node->filter('div.mb-4 .badge')->each(function(Crawler $countrieNode) {
                    return $countrieNode->text();
                });

                // Проверяем скачано ли изображение, если нет, то качаем
                $this->messageDispatch->dispatch(
                    new ParserCarModelImageMessage(
                        $model->getTitle(),
                        $model->getNamespace(),
                        $node->filter('.img-fluid.img-thumbnail')->attr('src'),
                    ),
                //                    stamps: [new MessageDelay('5 seconds')],
                //                    transport: (string) 'reference-car'
                );

                return [
                    'title' => preg_replace([
                        '/\s*\[\d+\s*\.\.\s*\d+\]|\d+\s*\.\.\s*\d+/',
                        '/\s+/u',
                        '/[«»"\'`“”‘’]/u',
                        '/^[\s\-]+|[\s\-]+$/u',
                    ], [
                        '',
                    ],
                        trim(
                        // Нормализация неразрывных пробелов перед обработкой
                            str_replace(["\u{A0}", "\xC2\xA0"], ' ',
                                $node->filter('a:first-child h2 .fw-400.text-nowrap')->text(),
                            ),
                        )),
                    'href' => $node->filter('a:first-child')->attr('href'),
                    'years' => $years,
                    'countries' => $countries,
                ];
            });

            // Получаем все года модели и оставляем только уникальные, сортируем от меньшего к большему
            $allModelYears = array_merge(...array_column($carModelGenerations, 'years'));
            $uniqueModelYears = array_unique($allModelYears);
            sort($uniqueModelYears);

            // По полученным годам генераций создаем клаасы годов модели
            //            $this->carModelYearClassCheckerService->checkModelYear($model, $uniqueModelYears);

            foreach($carModelGenerations as $carModelGeneration)
            {
                if(empty($carModelGeneration['title']))
                {
                    continue;
                }

                // Составляем имя класса поколения
                $carModelGeneration['class_name'] = preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $carModelGeneration['title'],
                    ),
                );

                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    new ParsercarModelGenerationMessage(
                        (string) $carModelGeneration['href'],
                        (string) $carModelGeneration['class_name'],
                        (string) $carModelGeneration['title'],
                        (array) $carModelGeneration['years'],
                        (array) $carModelGeneration['countries'],
                        (array) $carModelGeneration['model'] = $carModelArray,
                    ),
                //                    stamps: [new MessageDelay('5 seconds')],
                //                    transport: (string) 'reference-car'
                );
            }
        }
    }
}
