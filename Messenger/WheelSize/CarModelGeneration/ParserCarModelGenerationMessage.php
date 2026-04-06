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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarModelGeneration;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\ModelGenerations\CarModelGenerationsInterface;

final class ParserCarModelGenerationMessage
{

    /** Url поколения */
    private string $url;


    /** Имя класса поколения */
    private string $className;

    /** Имя поколения */
    private string $title;

    /** Года поколения */
    private array $years;

    /** Страны поколения */
    private array $countries;

    /** модель поколения */
    private array $model;


    public function __construct(
        string $url,
        string $className,
        string $title,
        array $years,
        array $countries,
        array $model
    )
    {
        $this->url = (string) $url;
        $this->className = (string) $className;
        $this->title = (string) $title;
        $this->years = (array) $years;
        $this->countries = (array) $countries;
        $this->model = (array) $model;
    }


    /** Url поколения */
    public function getUrl(): string
    {
        return (string) $this->url;
    }

    /** Namespace класса поколения */
    public function getNamespace(): string
    {
        return CarModelGenerationsInterface::GENERATION_NAMESPACE.'Collection\\'.$this->className;
    }

    /** Имя класса поколения */
    public function getClassName(): string
    {
        return $this->className;
    }

    /** Имя поколения */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Года поколения
     */
    public function getYears(): array
    {
        return $this->years;
    }

    /**
     * Страны поколения
     */
    public function getCountries(): array
    {
        return $this->countries;
    }

    /** Модель поколения */
    public function getModel(): array
    {
        return $this->model;
    }

    /**
     * Возвращает все поля сообщения в виде массива
     *
     * @return array{
     *     url: string,
     *     className: string,
     *     title: string,
     *     years: array,
     *     countries: array,
     *     model: array
     * }
     */

    public function getAll(): array
    {
        return [
            'href' => $this->url,
            'class_name' => $this->className,
            'title' => $this->title,
            'years' => $this->years,
            'countries' => $this->countries,
            'model' => $this->model,
        ];
    }
}
