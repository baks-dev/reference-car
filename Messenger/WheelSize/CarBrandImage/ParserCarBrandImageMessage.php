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

namespace BaksDev\Reference\Car\Messenger\WheelSize\CarBrandImage;

final class ParserCarBrandImageMessage
{
    /** Url картинки бренда */
    private string $imageSrc;

    /** Имя бренда */
    private string $title;

    /** Имя класса бренда */
    private string $className;

    /** Namespace класса бренда */
    private string $classNamespace;

    public function __construct(
        string $imageSrc,
        string $title,
        string $className,
        string $classNamespace
    )
    {
        $this->imageSrc = (string) $imageSrc;
        $this->title = (string) $title;
        $this->className = (string) $className;
        $this->classNamespace = (string) $classNamespace;
    }

    public function getImageSrc(): string
    {
        return $this->imageSrc;
    }

    /** Имя модели */
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    /** Namespace класса модели */
    public function getNamespace(): string
    {
        return $this->classNamespace;
    }


}
