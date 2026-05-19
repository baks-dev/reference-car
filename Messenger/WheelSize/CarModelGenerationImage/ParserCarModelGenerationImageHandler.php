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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarModelGenerationImage;

use BaksDev\Core\Deduplicator\Deduplicator;
use BaksDev\Core\Deduplicator\DeduplicatorInterface;
use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\CarModelGenerationsInterface;
use DateInterval;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
#[Autoconfigure(shared: false)]
final readonly class ParserCarModelGenerationImageHandler
{
    public function __construct(private DeduplicatorInterface $Deduplicator) {}


    /**
     * Сохраняем картинку модели
     */
    public function __invoke(ParserCarModelGenerationImageMessage $message): void
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

        echo 'Скачиваем картинку поколения '.$message->getTitle().PHP_EOL;

        $carModelGenerationImage = $message->getImageSrc();


        // Получаем класс модели и uid для создания имени картинки
        $generationFullNamespace = $message->getNamespace().$message->getClassName();


        /** @var CarModelGenerationsInterface $generationFullNamespace */
        $generationUid = $generationFullNamespace::getUid();


        // путь для закачивания файла
        $imagePath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            'Resources',
            'upload',
            'car_model_generation_image',
            $generationUid
        ]);


        // определяем расширение файла
        $extension = pathinfo(
            parse_url($carModelGenerationImage, PHP_URL_PATH),
            PATHINFO_EXTENSION
        ) ?: 'jpg';


        $filename = 'image.'.$extension;
        $fullPath = $imagePath.'/'.$filename;


        // Проверяем существует ли файл
        if(file_exists($fullPath))
        {
            echo 'Файл уже существует: '.$filename.PHP_EOL;
            return;
        }


        // Создаем директорию, если ее нет
        if(false === file_exists($imagePath))
        {
            mkdir($imagePath, 0777, true);
        }


        $context = stream_context_create([
            'http' => ['timeout' => 30], /* Таймаут 30 секунд */
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);

        $tempFile = tempnam(sys_get_temp_dir(), 'car_img');

        $content = file_get_contents($message->getImageSrc(), false, $context);

        if($content === false)
        {
            throw new RuntimeException('Не удалось загрузить изображение');
        }


        file_put_contents($tempFile, $content);

        $imageInfo = getimagesize($tempFile);
        if($imageInfo === false)
        {
            throw new RuntimeException('Неверный формат изображения');
        }

        $mime = $imageInfo['mime'];


        // Создаем изображение в зависимости от типа
        switch($mime)
        {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($tempFile);
                break;
            case 'image/png':
                $image = imagecreatefrompng($tempFile);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($tempFile);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($tempFile);
                break;
            default:
                throw new RuntimeException('Неподдерживаемый формат изображения: '.$mime);
        }

        if($image === false)
        {
            throw new RuntimeException('Не удалось создать изображение из файла');
        }

        $result = imagewebp($image, $fullPath, 80); // 80% качество

        if($result === false)
        {
            throw new RuntimeException('Не удалось сохранить изображение в формате WebP');
        }


        // Освобождаем память
        imagedestroy($image);


        echo 'Изображение успешно сохранено как WebP: '.$filename.PHP_EOL;

        if(file_exists($tempFile))
        {
            unlink($tempFile);
        }

        if(false === $message->isForced())
        {
            /** @var Deduplicator $Deduplicator */
            $Deduplicator->save();
        }
    }
}
