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

namespace BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\Collection\GACES9\Petrol20TPHEV367HP\Power;

use BaksDev\Reference\Car\Type\CarModelPetrols\Id\ModelPetrols\CarModelPetrolPowerPSInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarPetrolModels\Id\ModelPetrols\Collection\GACES9\Petrol20TPHEV367HP\ModelPetrol;

#[AutoconfigureTag('baks.car.model.petrols.power.ps')]
final class Power373PS implements CarModelPetrolPowerPSInterface
{
    /** Uid (ID) комплектации */
    public const string CAR_MODEL_PETROL_UID = ModelPetrol::CAR_MODEL_PETROL_UID;

    /** Значение параметра мощности */
    public const string CAR_MODEL_POWER_VALUE = '373';

    /** Единица измерения */
    public const string CAR_MODEL_POWER_MEASURE = 'PS';

    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = ['373'];

    public static function getUid(): string
    {
        return self::CAR_MODEL_PETROL_UID;
    }

    public static function getValue(): string
    {
        return self::CAR_MODEL_POWER_VALUE;
    }

    public static function getValueMeasure(): string
    {
        return self::CAR_MODEL_POWER_MEASURE;
    }

    public static function equals(mixed $value): bool
    {
        if(is_object($value) && method_exists($value, '__toString'))
        {
            $value = (string) $value;
        }

        if(is_string($value))
        {
            return array_any(
                self::HAYSTACK,
                static fn($item) => str_contains(mb_strtolower($value), mb_strtolower($item)),
            );
        }

        return false;
    }

    public static function sort(): int
    {
        return 2;
    }
}
