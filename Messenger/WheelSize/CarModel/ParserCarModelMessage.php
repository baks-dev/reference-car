<?php
/*
 *  Copyright 2024.  Baks.dev <admin@baks.dev>
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

use BaksDev\Reference\Car\Type\CarModels\Id\Models\CarModelsInterface;

final class ParserCarModelMessage
{

    /** Url модели */
    private string $url;

    /** Дата модели */
    private string $date;


    /** Имя класса модели */
    private string $className;

    /** Имя модели */
    private string $title;

    /** Бренд модели */
    private array $brand;


    public function __construct(
        string $url,
        string $date,
        string $className,
        string $title,
        //        string $image,
        array $brand
    )
    {
        $this->url = (string) $url;
        $this->date = (string) $date;
        $this->className = (string) $className;
        $this->title = (string) $title;
        //        $this->image = (string) $image;
        $this->brand = (array) $brand;
    }

    /** Url модели */
    public function getUrl(): string
    {
        return (string) $this->url;
    }

    /** Namespace класса модели */
    public function getNamespace(): string
    {
        return CarModelsInterface::MODEL_NAMESPACE.'Collection\\'.$this->className;
    }

    /** Имя класса модели */
    public function getClassName(): string
    {
        return $this->className;
    }

    /** Имя модели */
    public function getTitle(): string
    {
        return $this->title;
    }

    //    public function getImage(): string
    //    {
    //        return $this->image;
    //    }

    /** Бренд модели */
    public function getBrand(): array
    {
        return $this->brand;
    }

    /**
     * Возвращает все поля сообщения в виде массива
     *
     * @return array{
     *     url: string,
     *     date: string,
     *     class_name: string,
     *     title: string,
     * //     *     image: string,
     *     brand: array
     * }
     */

    public function getAll(): array
    {
        return [
            'href' => $this->url,
            'date' => $this->date,
            'class_name' => $this->className,
            'title' => $this->title,
            //            'image' => $this->image,
            'brand' => $this->brand,
        ];
    }
}
