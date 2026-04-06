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

use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\CarModelWheelsInterface;

final class ParserWheelMessage
{

    /** Имя класса шины */
    private string $className;

    /** Название папки связанного model petrol */
    private string $relatedModelPetrolDirName;

    /** Шина */
    private string $tire;

    /** Обод */
    private string $rim;

    /** Диапазон смещения */
    private string $offsetRange;

    /** Возврат */
    private string $backspacing;

    /** Вес шины */
    private string $tireWeight;

    /** бар */
    private string $bar;

    /** Поколение */
    private array $generation;

    public function __construct(
        string $className,
        string $relatedModelPetrolDirName,
        string $tire,
        string $rim,
        string $offsetRange,
        string $backspacing,
        string $tireWeight,
        string $bar,
        array $generation,
    )
    {
        $this->className = (string) $className;
        $this->relatedModelPetrolDirName = (string) $relatedModelPetrolDirName;
        $this->tire = (string) $tire;
        $this->rim = (string) $rim;
        $this->offsetRange = (string) $offsetRange;
        $this->backspacing = (string) $backspacing;
        $this->tireWeight = (string) $tireWeight;
        $this->bar = (string) $bar;
        $this->generation = (array) $generation;
    }

    /** Namespace класса шин */
    public function getNamespace(): string
    {
        return CarModelWheelsInterface::WHEEL_NAMESPACE.'Collection\\'.$this->className;
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

    /** Поколение */
    public function getGeneration(): array
    {
        return $this->generation;
    }

    /**
     * Возращает имя папки связанного model petrol
     */
    public function getRelatedModelPetrolDirName(): string
    {
        return $this->relatedModelPetrolDirName;
    }

    /**
     * Возвращает все поля сообщения в виде массива
     *
     * @return array{
     *     class_name: string,
     *     relatedModelPetrolDirName: string,
     *     tire: string,
     *     rim: string,
     *     offset_range: string,
     *     backspacing: string,
     *     tire_weight: string,
     *     bar: string,
     *     generation: array
     * }
     */
    public function getAll(): array
    {
        return [
            'class_name' => $this->className,
            'relatedModelPetrolDirName' => $this->relatedModelPetrolDirName,
            'tire' => $this->tire,
            'rim' => $this->rim,
            'offset_range' => $this->offsetRange,
            'backspacing' => $this->backspacing,
            'tire_weight' => $this->tireWeight,
            'bar' => $this->bar,
            'generation' => $this->generation,
        ];
    }
}
