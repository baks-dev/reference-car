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

use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Reference\Car\Command\Parser\RunParserWheelSizeCommand;
use BaksDev\Reference\Car\Messenger\WheelSize\WheelSize;
use DateInterval;
use Facebook\WebDriver\Exception\TimeoutException;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Autoconfigure(shared: false)]
final class ParserCarBrandRequest extends WheelSize
{
    /* Задержка между запросами в секундах */
    private const int REQUEST_DELAY = 4;

    public function __construct(
        #[Target('referenceCarLogger')] private readonly LoggerInterface $logger,
        private readonly DeduplicatorInterface $Deduplicator,
        private readonly CacheInterface $cache,
    ) {}


    /**
     * Получает HTML-контент по указанному URL.
     *
     * @param string $url URL для запроса
     *
     * @return string|false HTML-контент или false при ошибке
     */
    public function fetchHtml(string $url): string|false
    {
        $cacheKey = md5('parser_request_'.$url);
        $url = str_starts_with($url, 'http') ? $url : RunParserWheelSizeCommand::WHEEL_SIZE_URL.$url;


        /**
         * Получает HTML из кеша, либо получает html из запроса
         *
         * В случае false сохраняет в кеш на 1 сек, иначе кладет html в кеш на 1 неделю
         *
         * @return string|false HTML-контент или null при ошибке
         */
        return $this->cache->get($cacheKey, function(ItemInterface $item) use ($url): string|false
        {
            $item->expiresAfter(DateInterval::createFromDateString('1 second'));

            $client = $this->createClient();
            echo 'ParserCarBrandRequest.php Создали клиент для '.$url.PHP_EOL;

            sleep(self::REQUEST_DELAY);

            $this->logger->info('Выполняем запрос на '.$url);


            /**
             * Создаем дедупликатор для капчи, чтобы временно повторно не отправлять запросы на сайт во избежание
             * блокировки по IP
             */
            $Deduplicator = $this->Deduplicator
                ->namespace('reference-car')
                ->expiresAfter(DateInterval::createFromDateString('10 minute'))
                ->deduplication(['captcha', md5(self::class)]);

            if($Deduplicator->isExecuted())
            {
                return false;
            }

            $response = $client->request('GET', $url);

            try
            {
                $client->waitFor('.card-group');
            }
            catch(TimeoutException)
            {
                $this->logger->critical('Превышен лимит запросов к серверу', [$url, self::class.':'.__LINE__]);
                $Deduplicator->save();
                return false;
            }

            $content = $client->getPageSource();

            echo 'ParserCarBrandRequest.php Закрыли клиент для '.$url.PHP_EOL;
            $client->quit();

            if(empty($content))
            {
                $this->logger->error('Ошибка запроса на '.$url.' статус ответа: '.$response->getStatusCode());
                return false;
            }

            $item->expiresAfter(DateInterval::createFromDateString('1 week'));

            return $content;
        });
    }
}
