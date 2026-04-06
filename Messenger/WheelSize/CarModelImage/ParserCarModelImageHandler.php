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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarModelImage;


use BaksDev\Reference\Car\BaksDevReferenceCarBundle;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(priority: 0)]
final class ParserCarModelImageHandler
{
    protected LoggerInterface $logger;

    private const NAMESPACE = [
        "Type",
        "CarModels",
        "Id",
        "Models",
        "Collection",
    ];

    private string $collectionPath;

    private string $modelFullNamespace;

    public function __construct(
        #[Target('referenceCarLogger')] LoggerInterface $logger,
    )
    {
        $this->logger = $logger;
        $this->collectionPath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            implode(DIRECTORY_SEPARATOR, self::NAMESPACE),
        ]);
        $this->modelFullNamespace = implode('\\', [
            rtrim(BaksDevReferenceCarBundle::NAMESPACE, '\\'),
            ...self::NAMESPACE]);
    }

    /**
     * Сохраняем картинку модели
     *
     * @param ParserCarModelImageMessage $message
     *
     * @return void
     */
    public function __invoke(ParserCarModelImageMessage $message): void
    {
        echo 'Скачиваем картинку модели '.$message->getTitle().PHP_EOL;

        // Получаем класс модели и uid для создания имени картинки
        $modelFullNamespace = implode('\\', [
            $message->getNamespace(),
        ]);

        $modelUid = $modelFullNamespace::getUid();

        // путь для закачивания файла
        $imagePath = implode(DIRECTORY_SEPARATOR, [
            rtrim(BaksDevReferenceCarBundle::PATH, DIRECTORY_SEPARATOR),
            'Resources',
            'assets',
            'reference-car',
            'image',
            'Model',
        ]);

        $filename = $modelUid.'.webp';
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
            'http' => [
                'timeout' => 30, // Таймаут 30 секунд
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $tempFile = tempnam(sys_get_temp_dir(), 'car_img');

        $content = file_get_contents($message->getImage(), false, $context);

        if($content === false)
        {
            throw new RuntimeException('Не удалось загрузить изображение');
        }

        if($content !== false)
        {
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
        }
    }
}
