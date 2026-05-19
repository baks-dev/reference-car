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

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Core\Messenger\MessageDelay;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Command\Parser\RunParserWheelSizeCommand;
use BaksDev\Reference\Car\Messenger\WheelSize\CarBrandImage\ParserCarBrandImageMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModel\ParserCarModelMessage;
use BaksDev\Reference\Car\Service\CarBrand\CarBrandClassCheckerDTO;
use BaksDev\Reference\Car\Service\CarBrand\CarBrandClassCheckerService;
use BaksDev\Reference\Car\Type\CarBrands\CarBrand;
use DateInterval;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserCarBrandInvariableHandler
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private DeduplicatorInterface $Deduplicator,
        private MessageDispatchInterface $messageDispatch,
        private CarBrandClassCheckerService $carBrandClassCheckerService,
        private ParserCarBrandRequest $parserCarBrandRequest
    ) {}

    public function __invoke(ParserCarBrandMessage $message): void
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

        $CarBrandClassCheckerDTO = new CarBrandClassCheckerDTO($message->getClassName(), $message->getTitle());


        // Проверяем есть ли класс бренда. Если нет, то генерируем
        $this->carBrandClassCheckerService->checkBrand($CarBrandClassCheckerDTO);


        // Парсим внутреннюю страницу бренда
        $this->parseBrands($message);


        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }


    /**
     * Парсит внутренние страницы брендов
     */
    private function parseBrands(ParserCarBrandMessage $message): void
    {
        echo 'Начинаем парсить бренд '.$message->getTitle().' по url: '.$message->getUrl().PHP_EOL;
        $this->logger->info('Начинаем парсить бренд '.$message->getTitle().' по url: '.$message->getUrl());


        // Получаем HTML с внутренней страницы бренда
        $html = $this->parserCarBrandRequest->fetchHtml($message->getUrl());
        $crawler = new Crawler($html, $message->getUrl());


        // Получаем изображения бренда для скачивания
        $carBrandImage = $crawler->filter('.figure-img.img-fluid.rounded.float-right.float-sm-none.mr-2');


        if($carBrandImage->count() > 0)
        {
            $sleep = new Randomizer()->getInt(3, 7);

            if(false === RunParserWheelSizeCommand::IS_ASYNC)
            {
                sleep($sleep);
            }

            $this->messageDispatch->dispatch(
                new ParserCarBrandImageMessage(
                    $carBrandImage->attr('src'),
                    $message->getTitle(),
                    $message->getClassName(),
                    $message->getNamespace(),
                    $message->isForced()
                ),
                stamps: RunParserWheelSizeCommand::IS_ASYNC ? [new MessageDelay(sprintf(
                    '%s seconds',
                    $sleep
                ))] : [],
                transport: RunParserWheelSizeCommand::IS_ASYNC ? 'reference-car' : null
            );
        }


        // Ищет список моделей
        $cardGroup = $crawler->filter('.divideIntoColumns');


        // Если модели найдены, то начинает получать значения с HTML
        if($cardGroup->count() > 0)
        {
            $carModels = $cardGroup
                ->filter('a')
                ->each(function(Crawler $node)
                    {
                        return [
                            'href' => $node->attr('href'),
                            'title' => trim($node->filter('.model-name')->text()),
                        ];
                    }
                );

            foreach($carModels as $carModel)
            {
                // Составляем имя класса модели
                $carModel['class_name'] = $message->getClassName().preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $carModel['title'],
                    ),
                );


                $sleep = new Randomizer()->getInt(3, 7);

                if(false === RunParserWheelSizeCommand::IS_ASYNC)
                {
                    sleep($sleep);
                }


                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    new ParserCarModelMessage(
                        $carModel['href'],
                        $carModel['class_name'],
                        $carModel['title'],
                        new CarBrand($message->getTitle(), $message->getNamespace()),
                        $message->isForced(),
                    ),
                    stamps: RunParserWheelSizeCommand::IS_ASYNC ? [new MessageDelay(sprintf(
                        '%s seconds',
                        $sleep
                    ))] : [],
                    transport: RunParserWheelSizeCommand::IS_ASYNC ? 'reference-car' : null
                );
            }
        }
    }
}
