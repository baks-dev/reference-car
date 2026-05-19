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

use BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\CarModelGenerationsInterface;
use BaksDev\Reference\Car\Type\CarModels\CarModel;

final readonly class ParserCarModelGenerationMessage
{
    public function __construct(
        private string $url,
        private string $className,
        private string $title,
        private CarModel $model,
        private ?bool $isForced = false,
    ) {}


    /** Url поколения */
    public function getUrl(): string
    {
        return $this->url;
    }


    /** Namespace класса поколения */
    public function getNamespace(): string
    {
        return CarModelGenerationsInterface::GENERATION_NAMESPACE.'Collection\\';
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


    /** Модель поколения */
    public function getModel(): CarModel
    {
        return $this->model;
    }


    public function isForced(): bool
    {
        return $this->isForced;
    }
}
