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

namespace BaksDev\Reference\Car\Messenger\WheelSize\Wheel;

use BaksDev\Reference\Car\Type\CarModelPetrols\CarModelPetrol;
use BaksDev\Reference\Car\Type\CarModelWheels\ModelWheels\CarModelWheelsInterface;

final readonly class ParserWheelMessage
{
    public function __construct(
        private string $className,
        private string $tire,
        private string $rim,
        private string $offsetRange,
        private string $backspacing,
        private string $tireWeight,
        private string $bar,
        private CarModelPetrol $modelPetrol,
        private ?bool $isForced = false,
    ) {}


    /** Namespace класса шин */
    public function getNamespace(): string
    {
        return CarModelWheelsInterface::WHEEL_NAMESPACE.'Collection\\';
    }


    /** Имя класса шины */
    public function getClassName(): string
    {
        return $this->className;
    }


    /** Шина */
    public function getTire(): string
    {
        return $this->tire;
    }


    /** Обод */
    public function getRim(): string
    {
        return $this->rim;
    }


    /** Диапазон смещения */
    public function getOffsetRange(): string
    {
        return $this->offsetRange;
    }


    /** Возврат */
    public function getBackspacing(): string
    {
        return $this->backspacing;
    }


    /** Вес шины */
    public function getTireWeight(): string
    {
        return $this->tireWeight;
    }


    /** Бар */
    public function getBar(): string
    {
        return $this->bar;
    }


    public function getModelPetrol(): CarModelPetrol
    {
        return $this->modelPetrol;
    }


    public function isForced(): bool
    {
        return $this->isForced;
    }
}
