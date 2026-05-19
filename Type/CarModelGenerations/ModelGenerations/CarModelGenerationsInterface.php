<?php
/*
 * Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations;

use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('baks.car.generations')]
interface CarModelGenerationsInterface
{
    public const string GENERATION_NAMESPACE = __NAMESPACE__.'\\';


    /** Отдает uid поколения */
    public static function getUid(): CarModelGenerationUid;


    /**
     * Возвращает значение (value)
     */
    public static function getValue(): CarModelGenerationName;


    /** Отдает uid привязанной модели */
    public static function getModelUid(): CarModelUid;


    /**
     * Проверяет, относится ли строка к данному объекту
     */
    public static function equals(mixed $value): bool;


    /**
     * Сортировка (чем меньше число - тем первым в итерации будет значение)
     */
    public static function sort(): int;

}
