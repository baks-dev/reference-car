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

namespace BaksDev\Reference\Car\Service\CarModelPetrol;

use BaksDev\Reference\Car\Type\CarModelGenerations\CarModelGeneration;
use BaksDev\Reference\Car\Type\CarModelPetrols\ModelPetrols\CarModelPetrolInterface;

final readonly class CarModelPetrolClassCheckerDTO
{
    public function __construct(
        private string $className,
        private string $title,
        private int $hp,
        private int $kw,
        private int $ps,
        private string $year,
        private CarModelGeneration $generation
    ) {}


    /** Namespace класса комплектации */
    public function getNamespace(): string
    {
        return CarModelPetrolInterface::MODEL_PETROL_NAMESPACE.'Collection\\';
    }


    /** Имя класса комплектации */
    public function getClassName(): string
    {
        return $this->className;
    }


    /** Имя комплектации */
    public function getTitle(): string
    {
        return $this->title;
    }


    public function getGeneration(): CarModelGeneration
    {
        return $this->generation;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getKw(): int
    {
        return $this->kw;
    }

    public function getPs(): int
    {
        return $this->ps;
    }

    public function getYear(): string
    {
        return $this->year;
    }
}
