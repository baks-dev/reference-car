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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarBrandImage;

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Type\CarBrands\Brands\CarBrandsInterface;
use DateInterval;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserCarBrandImageHandler
{
    public function __construct(private DeduplicatorInterface $Deduplicator) {}


    /**
     * Сохраняем изображение бренда
     */
    public function __invoke(ParserCarBrandImageMessage $message): void
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

        echo 'Скачиваем картинку бренда '.$message->getTitle().PHP_EOL;

        $carBrandImage = $message->getImageSrc();


        // Получаем класс модели и uid для создания имени картинки
        $carBrandFullNamespace = $message->getNamespace().$message->getClassName();


        /** @var CarBrandsInterface $carBrandFullNamespace */
        $carBrandUid = $carBrandFullNamespace::getUid();


        // путь для закачивания файла
        $imagePath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            'Resources',
            'upload',
            'car_brand_image',
            $carBrandUid
        ]);


        // определяем расширение файла
        $extension = pathinfo(parse_url($carBrandImage, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';


        $filename = 'image.'.$extension;
        $fullPath = $imagePath.'/'.$filename;


        // Проверяем существует ли файл
        if(file_exists($fullPath))
        {
            echo 'Файл уже существует: '.$filename.PHP_EOL;
            return;
        }


        // Создаем директорию, если ее нет
        if(!file_exists($imagePath))
        {
            mkdir($imagePath, 0777, true);
        }


        $context = stream_context_create([
            'http' => ['timeout' => 30, /* Таймаут 30 секунд */ ],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ]);

        $content = file_get_contents($carBrandImage, false, $context);

        if($content !== false)
        {
            file_put_contents($fullPath, $content);
        }

        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }
}
