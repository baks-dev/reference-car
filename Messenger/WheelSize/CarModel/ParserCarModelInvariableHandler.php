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

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Core\Messenger\MessageDelay;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Command\Parser\RunParserWheelSizeCommand;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModelGeneration\ParserCarModelGenerationMessage;
use BaksDev\Reference\Car\Service\CarModel\CarModelClassCheckerDTO;
use BaksDev\Reference\Car\Service\CarModel\CarModelClassCheckerService;
use BaksDev\Reference\Car\Type\CarModels\CarModel;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateInterval;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserCarModelInvariableHandler
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private DeduplicatorInterface $Deduplicator,
        private MessageDispatchInterface $messageDispatch,
        private CarModelClassCheckerService $carModelClassCheckerService,
        private ParserCarModelRequest $parserModelRequest
    ) {}

    public function __invoke(ParserCarModelMessage $message): void
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

        $CarModelClassCheckerDTO = new CarModelClassCheckerDTO(
            $message->getClassName(),
            $message->getTitle(),
            $message->getBrand()
        );


        // Проверяем есть ли класс модели. Если нет, то генерируем
        $this->carModelClassCheckerService->checkModel($CarModelClassCheckerDTO);


        // Парсим внутреннюю страницу модели
        $this->parseModel($message);


        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }


    /**
     * Парсит внутренние страницы моделей
     */
    private function parseModel(ParserCarModelMessage $message): void
    {
        echo 'Начинаем парсить модель '.$message->getTitle().PHP_EOL;
        $this->logger->info('Начинаем парсить модель '.$message->getTitle().' по url: '.$message->getUrl());


        // Получаем HTML с внутренней страницы модели
        $html = $this->parserModelRequest->fetchHtml($message->getUrl());


        /**
         * Если ошибка капчи или по иной причине нет контента на странице - пробрасываем отложенное сообщение на этот же
         * диспатчер
         */
        if(false === $html)
        {
            $this->messageDispatch->dispatch(
                $message,
                stamps: [new MessageDelay('1 hour')],
                transport: 'reference-car'
            );

            return;
        }


        $crawler = new Crawler($html, RunParserWheelSizeCommand::WHEEL_SIZE_URL.$message->getUrl());


        // Ищет список поколений
        $carModelGenerationsGroup = $crawler->filter('.col-md-12.col-sm-12.col-lg-8 .mb-4');


        // Если поколения найдены, то начинает получать значения с HTML
        if($carModelGenerationsGroup->count() > 0)
        {
            $carModelGenerations = $carModelGenerationsGroup
                ->filter('.market-generation')
                ->each(function(Crawler $node) use ($message)
                {
                    $years = $node->filter('div.mb-4 a span span')->each(function(Crawler $yearNode) {
                        return $yearNode->text();
                    });

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
                                    $node
                                        ->filter('a:first-child h2 .fw-400.text-nowrap')
                                        ->text(),
                                ),
                            )),
                        'href' => $node
                            ->filter('a:first-child')
                            ->attr('href'),
                        'years' => $years,
                        'imageSrc' => $node
                            ->filter('.img-fluid.img-thumbnail')
                            ->attr('src'),
                    ];
                });

            foreach($carModelGenerations as $carModelGeneration)
            {
                $title = (false === empty($carModelGeneration['title'])) ?
                    $message->getTitle().' '.$carModelGeneration['title'].' ('.$carModelGeneration['years'][0].'-'.$carModelGeneration['years'][1].')' :
                    $message->getTitle().' ('.$carModelGeneration['years'][0].'-'.$carModelGeneration['years'][1].')';


                // Составляем имя класса поколения
                $carModelGeneration['class_name'] = $message->getClassName().preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $carModelGeneration['title'].$carModelGeneration['years'][0].$carModelGeneration['years'][1]
                    ),
                );


                $sleep = new Randomizer()->getInt(7, 10);

                if(false === RunParserWheelSizeCommand::IS_ASYNC)
                {
                    sleep($sleep);
                }


                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    message: new ParsercarModelGenerationMessage(
                        $carModelGeneration['href'],
                        $carModelGeneration['class_name'],
                        $title,
                        new CarModel($message->getClassName(), $message->getNamespace(), $message->getBrand()),
                        $message->isForced()
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
