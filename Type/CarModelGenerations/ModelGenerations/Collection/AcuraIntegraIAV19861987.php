<?php
/*
 *  Copyright 2026.  Baks.dev <admin@baks.dev>
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

namespace BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\Collection;

use BaksDev\Reference\Car\Type\CarModelGenerations\ModelGenerations\CarModelGenerationsInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModels\Models\Collection\AcuraIntegra as Model;
use BaksDev\Reference\Car\Type\CarModelGenerations\Id\CarModelGenerationUid;
use BaksDev\Reference\Car\Type\CarModels\Id\CarModelUid;
use BaksDev\Reference\Car\Type\CarModelGenerations\Name\CarModelGenerationName;

#[AutoconfigureTag('baks.car.generations')]
final class AcuraIntegraIAV19861987 implements CarModelGenerationsInterface
{
    /** Uid (ID) поколения */
    public const string CAR_GENERATION_UID = '019e3b3b-10d4-7f7b-997a-ce504bcd6530';


    /** Значение названия поколения */
    public const string CAR_GENERATION_VALUE = 'Integra I(AV) (1986-1987)';


    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = [self::CAR_GENERATION_VALUE, self::CAR_GENERATION_UID];


    /** Uid (ID) модели */
    public const string CAR_MODEL_UID = Model::CAR_MODEL_UID;


    /**
    * Возвращает UID поколения
    */
    public static function getUid(): CarModelGenerationUid
    {
        return new CarModelGenerationUid(self::CAR_GENERATION_UID);
    }


    /**
    * Возвращает значение (value)
    */
    public static function getValue(): CarModelGenerationName
    {
        return new CarModelGenerationName(self::CAR_GENERATION_VALUE);
    }


    /**
    * Возвращает UID привязанной модели
    */
    public static function getModelUid(): CarModelUid
    {
        return new CarModelUid(self::CAR_MODEL_UID);
    }


    public static function equals(mixed $value): bool
    {
        if (is_object($value) && method_exists($value, '__toString'))
        {
            $value = (string)$value;
        }

        if (is_string($value))
        {
            return array_any(self::HAYSTACK, static fn($item) => (mb_strtolower($value) === mb_strtolower($item)));
        }

        return false;
    }

    public static function sort(): int
        {
            return 2;
        }
    }
