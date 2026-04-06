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

namespace BaksDev\Reference\Car\Type\CarModelWheels\Diameter\ModelWheels\Collection\TTRS;

use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\ModelWheels\CarModelWheelsDiameterInterface;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\ModelWheels\Collection\TTRS\OE24535ZR1993Y as CarModelWheel;
use BaksDev\Reference\Car\Type\CarModelWheels\Id\CarModelWheelUid;
use BaksDev\Reference\Car\Type\CarModelWheels\Diameter\CarModelWheelDiameter;

#[AutoconfigureTag('baks.car.model.wheels.diameter')]
final class OE24535ZR1993Y implements CarModelWheelsDiameterInterface
{
    /** Uid (ID) колес */
    public const string CAR_MODEL_WHEEL_UID = CarModelWheel::CAR_MODEL_WHEEL_UID;

    /** Значение */
    public const string CAR_MODEL_WHEEL_DIAMETER_VALUE = '19';

    /** @var string[] Список для фильтрации */
    public const array HAYSTACK = ['19', self::CAR_MODEL_WHEEL_UID];

    public static function getUid(): CarModelWheelUid
    {
        return new CarModelWheelUid(self::CAR_MODEL_WHEEL_UID);
    }

    public static function getValue(): CarModelWheelDiameter
    {
        return new CarModelWheelDiameter(self::CAR_MODEL_WHEEL_DIAMETER_VALUE);
    }

    public static function equals(mixed $value): bool
    {
        if (is_object($value) && method_exists($value, '__toString')) {
            $value = (string)$value;
        }

        if (is_string($value)) {
            return array_any(
                self::HAYSTACK,
                static fn($item) => mb_strtolower($value) === mb_strtolower($item)
            );
        }

        return false;
    }

    public static function sort(): int
    {
        return 2;
    }
}
