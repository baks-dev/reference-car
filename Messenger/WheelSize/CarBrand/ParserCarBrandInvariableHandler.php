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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarBrand;

use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Messenger\WheelSize\CarBrandImage\ParserCarBrandImageMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModel\ParserCarModelMessage;
use BaksDev\Reference\Car\Service\CarBrand\CarBrandClassCheckerService;
use BaksDev\Reference\Car\Service\CarBrand\CarBrandNameClassCheckerService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserCarBrandInvariableHandler
{
    /* Протокол и домен парсинга */
    private const WHEEL_SIZE_URI = 'https://www.wheel-size.com';

    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private MessageDispatchInterface $messageDispatch,
        private CarBrandClassCheckerService $carBrandClassCheckerService,
        private CarBrandNameClassCheckerService $carBrandNameClassCheckerService,
        private ParserCarBrandRequest $parserCarBrandRequest
    ) {}

    public function __invoke(ParserCarBrandMessage $message): void
    {
        // Проверяем есть ли класс бренда. Если нет, то генерируем
        $this->carBrandClassCheckerService->checkBrand($message);

        // Проверяем есть ли класс названия бренда. Если нет, то генерируем
        $this->carBrandNameClassCheckerService->checkBrandName($message);

        // Парсим внутреннюю страницу бренда
        $this->parseBrands($message);
    }

    /**
     * Парсит внутренние страницы брендов
     *
     * @param ParserCarBrandMessage $message
     *
     * @return void
     */
    private function parseBrands(ParserCarBrandMessage $message): void
    {
        $carBrand = $message->getAll();
        echo $carBrand['title'].PHP_EOL;

        echo 'Начинаем парсить бренд '.$carBrand['title'].' по url: '.$carBrand['href'].PHP_EOL;

        // Получаем HTML с внутренней страницы бренда
        $html = $this->parserCarBrandRequest->fetchHtml($carBrand['href']);
        $crawler = new Crawler($html, $carBrand['href']);

        // Скачиваем картинку бренда
        $carBrandImage = $crawler->filter('.figure-img.img-fluid.rounded.float-right.float-sm-none.mr-2');

        if($carBrandImage->count() > 0)
        {
            //        $this->saveBrandImage($crawler, $message);
            $this->messageDispatch->dispatch(
                new ParserCarBrandImageMessage(
                    $carBrandImage->attr('src'),
                    $message->getTitle(),
                    $carBrand['class_name'],
                    $message->getNamespace(),
                ),
            //                stamps: [new MessageDelay('5 seconds')],
            //                transport: (string) 'reference-car'
            );
        }

        // Ищет список моделей
        $cardGroup = $crawler->filter('.divideIntoColumns');

        // Если модели найдены, то начинает получать значения с HTML
        if($cardGroup->count() > 0)
        {
            $carModels = $cardGroup->filter('a')->each(function(Crawler $node) {
                return [
                    'href' => $node->attr('href'),
                    'title' => trim($node->filter('.model-name')->text()),
                    //                    'image' => trim($node->filter('.img-responsive.img-fluid.img-typical')->attr('src')),
                    'date' => trim($node->filter('.fw-300.d-block')->text()),
                ];
            });

            foreach($carModels as $carModel)
            {
                // Составляем имя класса модели
                $carModel['class_name'] = preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $carModel['title'],
                    ),
                );

                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    new ParserCarModelMessage(
                        (string) $carModel['href'],
                        (string) $carModel['date'],
                        (string) $carModel['class_name'],
                        (string) $carModel['title'],
                        //                        (string) $carModel['image'],
                        (array) $carModel['brand'] = $carBrand,
                    ),
                //                    stamps: [new MessageDelay('5 seconds')],
                //                    transport: (string) 'reference-car'
                );
            }
        }
    }
}
