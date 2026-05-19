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

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Core\Messenger\MessageDelay;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Command\Parser\RunParserWheelSizeCommand;
use BaksDev\Reference\Car\Messenger\WheelSize\CarModelGenerationImage\ParserCarModelGenerationImageMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\Petrol\ParserCarPetrolMessage;
use BaksDev\Reference\Car\Service\CarModelGeneration\CarModelGenerationClassCheckerDTO;
use BaksDev\Reference\Car\Service\CarModelGeneration\CarModelGenerationClassCheckerService;
use BaksDev\Reference\Car\Type\CarModelGenerations\CarModelGeneration;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use DateInterval;

#[Autoconfigure(shared: false)]
#[AsMessageHandler(priority: 0)]
final readonly class ParserCarModelGenerationInvariableHandler
{
    public function __construct(
        #[Target('referenceCarLogger')] private LoggerInterface $logger,
        private DeduplicatorInterface $Deduplicator,
        private MessageDispatchInterface $messageDispatch,
        private CarModelGenerationClassCheckerService $carModelGenerationClassCheckerService,
        private ParserCarModelGenerationRequest $parserCarModelGenerationRequest
    ) {}

    public function __invoke(ParserCarModelGenerationMessage $message): void
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

        $CarModelGenerationClassCheckerDTO = new CarModelGenerationClassCheckerDTO(
            $message->getClassName(),
            $message->getTitle(),
            $message->getModel(),
        );


        // Проверяем есть ли класс поколения модели. Если нет, то генерируем
        $this->carModelGenerationClassCheckerService->checkModelGeneration($CarModelGenerationClassCheckerDTO);


        // Парсим внутреннюю страницу поколения
        $this->parseGeneration($message);


        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }


    /**
     * Парсит внутренние страницы поколений
     */
    private function parseGeneration(ParserCarModelGenerationMessage $message): void
    {
        echo 'Начинаем парсить поколение '.$message->getTitle().' с url: '.$message->getUrl().PHP_EOL;
        $this->logger->info(
            'Начинаем парсить поколение '.$message->getTitle().' с url: '.$message->getUrl(),
        );


        // Получаем HTML с внутренней страницы поколения
        $html = $this->parserCarModelGenerationRequest->fetchHtml($message->getUrl());
        $crawler = new Crawler($html, RunParserWheelSizeCommand::WHEEL_SIZE_URL.$message->getUrl());

        $sleep = new Randomizer()->getInt(10, 100);

        if(false === RunParserWheelSizeCommand::IS_ASYNC)
        {
            sleep($sleep);
        }

        $imageSrc = $crawler->filter('img.d-block.w-100.img-fluid')->attr('src');


        // Проверяем скачано ли изображение, если нет, то качаем
        $this->messageDispatch->dispatch(
            new ParserCarModelGenerationImageMessage(
                $imageSrc,
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


        // Ищет список комплектаций
        $petrolGroup = $crawler->filter('.panel.engines-list');


        // Если комплектации найдены, то начинает получать значения с HTML
        if($petrolGroup->count() > 0)
        {
            $carPetrols = $petrolGroup
                ->filter('.mt-2 a.region-engine')
                ->each(function(Crawler $node) use ($message)
                {
                    return [
                        'title' => preg_replace([
                            '/\s*\[\d+\s*\.\.\s*\d+\]|\d+\s*\.\.\s*\d+/',
                            '/\s+/u',
                            '/[«»"\'`“”‘’]/u',
                            '/^[\s\-]+|[\s\-]+$/u',
                        ], [''], str_replace('.', '', trim(
                            // Нормализация неразрывных пробелов перед обработкой
                                str_replace(["\u{A0}", "\xC2\xA0"], ' ',
                                    $node->filter('span.position-relative')->text().
                                    $node->filter('span.pos-right')->text(),
                                ),
                            )
                        )),
                        'href' => rtrim($message->getUrl(), '/').$node->attr('href'),
                    ];
                });

            foreach($carPetrols as $carPetrol)
            {
                // Составляем имя класса комплектации
                $carPetrol['class_name'] = $message->getClassName().preg_replace(
                        '/[^a-zA-Z0-9]/',
                        '',
                        str_replace(
                            [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                            '',
                            $carPetrol['title'],
                        ),
                    );

                $sleep = new Randomizer()->getInt(3, 7);

                if(false === RunParserWheelSizeCommand::IS_ASYNC)
                {
                    sleep($sleep);
                }

                $this->messageDispatch->dispatch(
                    message: new ParserCarPetrolMessage(
                        $carPetrol['href'],
                        $carPetrol['class_name'],
                        $carPetrol['title'],
                        new CarModelGeneration(
                            $message->getClassName(),
                            $message->getNamespace(),
                            $message->getModel()
                        ),
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
