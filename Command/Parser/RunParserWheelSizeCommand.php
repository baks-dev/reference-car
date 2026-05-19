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

namespace BaksDev\Reference\Car\Command\Parser;

use BaksDev\Core\Command\Update\ProjectUpgradeInterface;
use BaksDev\Core\Messenger\MessageDelay;
use BaksDev\Core\Messenger\MessageDispatchInterface;
use BaksDev\Reference\Car\Messenger\WheelSize\CarBrand\ParserCarBrandMessage;
use BaksDev\Reference\Car\Messenger\WheelSize\MainPage\ParserMainPageRequest;
use Psr\Log\LoggerInterface;
use Random\Randomizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\DomCrawler\Crawler;

#[AsCommand(
    name: 'baks:reference-car:parser:run',
    description: 'Запуск парсера',
)]
#[AutoconfigureTag('baks.project.upgrade')]
class RunParserWheelSizeCommand extends Command implements ProjectUpgradeInterface
{
    /* URL начала парсинга */
    public const string WHEEL_SIZE_URL = 'https://www.wheel-size.com';


    public const bool IS_ASYNC = false;

    public function __construct(
        #[Target('referenceCarLogger')] private readonly LoggerInterface $logger,
        private readonly MessageDispatchInterface $messageDispatch,
        private readonly ParserMainPageRequest $parserMainPageRequest
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Парсинг в обход дедубликатора ((--force || -f))'
        );
    }


    /** Чам выше число - тем первее в итерации будет значение */
    public static function priority(): int
    {
        return 100;
    }


    /**
     * Выполняет команду по парсингу
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->text("Начинаем парсинг сайта wheel-size.com");
        $this->logger->info('Начинаем парсинг сайта wheel-size.com');


        // получаем HTML с главной страницы
        $html = $this->parserMainPageRequest->fetchHtml(self::WHEEL_SIZE_URL.'/size/');


        $crawler = new Crawler($html, self::WHEEL_SIZE_URL.'/size/');


        // Ищет список брендов
        $brandList = $crawler->filter('.brand-list-others');


        // Если бренды найдены, то начинает получать значения с HTML
        if($brandList->count() > 0)
        {
            $brands = $brandList->filter('a.brand-link-item')->each(function(Crawler $node) {
                return [
                    'href' => $node->attr('href'),
                    'title' => trim($node->filter('.brand-name')->text()),
                ];
            });

            //Поскольку парсится все, то много одинаковых элементов. Оставляем уникальные
            $brands = array_values(array_column($brands, null, 'title'));

            foreach($brands as $brand)
            {
                // Составляем имя класса бренда
                $brand['class_name'] = preg_replace(
                    '/[^a-zA-Z0-9]/',
                    '',
                    str_replace(
                        [' ', '-', '.', '[', ']', '/', '(', ')', '&', ':'],
                        '',
                        $brand['title'],
                    ),
                );

                $sleep = new Randomizer()->getInt(3, 7);

                if(false === RunParserWheelSizeCommand::IS_ASYNC)
                {
                    sleep($sleep);
                }


                // Отправляяем данные в очередь
                $this->messageDispatch->dispatch(
                    message: new ParserCarBrandMessage(
                        (string) $brand['href'],
                        (string) $brand['class_name'],
                        (string) $brand['title'],
                        $input->getOption('force')
                    ),
                    stamps: self::IS_ASYNC ? [new MessageDelay(sprintf(
                        '%s seconds',
                        $sleep
                    ))] : [],
                    transport: self::IS_ASYNC ? 'reference-car' : null
                );
            }
        }

        $io->text("Парсинг завершен");
        $this->logger->info('Парсинг успешно завершен!');

        return Command::SUCCESS;
    }
}


